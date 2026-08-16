#!/usr/bin/env python3
"""
Generate product mockups for the Pakka Patriot merch range.

For every design (tajmahal, hampi, ...) the design artwork is extracted from
the t-shirt colourway PNGs (line art on a near-white background) and composited
onto flat-style product mockups: hoodie, mug, tote bag, poster, sticker,
notebook and cap.

Output: <laravel>/storage/app/public/mockups/{category}/{design}.png (800x800)
"""

import math
import os

from PIL import Image, ImageDraw, ImageFilter

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
STORAGE = os.path.join(ROOT, "storage", "app", "public")
OUT_DIR = os.path.join(STORAGE, "mockups")

# Source folders: the website project's public assets (siblings of this app).
DESIGN_FOLDER = os.path.join(ROOT, "..", "public", "design")
TSHIRTS_FOLDER = os.path.join(ROOT, "..", "public", "tshirts")

# All merch designs. Preferred artwork is <public>/design/<slug>.png; when a
# design has no file there (hampi/konark), fall back to the matching
# terracotta colourway in <public>/tshirts/<slug>/.
DESIGNS = ["tajmahal", "hampi", "indiagate", "khajuraho", "konark", "netaji", "chanakya", "savarkar", "shivaji"]

# design slug -> tshirts colourway filename (fallback artwork)
TSHIRT_ART = {
    "hampi": "hampi_terracotta.png",
    "konark": "konark_terracotta.png",
}

# Category key (matches the product SKU segment) -> product noun for filenames.
CATEGORIES = ["hoodie", "mug", "tote-bag", "poster", "sticker-pack", "notebook", "cap"]

SIZE = 800


def vgrad(size, top, bottom):
    """Vertical gradient image."""
    w, h = size
    img = Image.new("RGB", size)
    for y in range(h):
        t = y / max(h - 1, 1)
        row = tuple(int(top[i] + (bottom[i] - top[i]) * t) for i in range(3))
        ImageDraw.Draw(img).line([(0, y), (w, y)], fill=row)
    return img


def extract_design(path):
    """Key the background out of the design artwork and return it as a dense,
    dark RGBA image cropped to its content bounding box.

    The design-folder files are fine terracotta line-art on a light background
    (only 3-9% ink), which vanishes when scaled down onto a mug/hoodie/etc.
    So beyond removing the background we (1) firm up the alpha so strokes stay
    opaque after downscaling, (2) dilate the strokes slightly so fine lines
    survive small print sizes, and (3) darken the ink so the design reads
    clearly on light product surfaces."""
    im = Image.open(path).convert("RGB")
    w, h = im.size
    px = im.load()

    corners = []
    for x in range(4):
        for y in range(4):
            corners.append(px[x, y])
            corners.append(px[w - 1 - x, y])
            corners.append(px[x, h - 1 - y])
            corners.append(px[w - 1 - x, h - 1 - y])
    bg = tuple(sum(c[i] for c in corners) // len(corners) for i in range(3))

    rgba = Image.new("RGBA", im.size)
    out = rgba.load()
    for y in range(h):
        for x in range(w):
            p = px[x, y]
            d = math.sqrt(sum((p[i] - bg[i]) ** 2 for i in range(3)))
            # firm alpha: anything clearly off the background is fully opaque
            alpha = 0 if d <= 26 else 255
            # darken the ink (~55% of the original colour) for contrast
            col = tuple(int(p[i] * 0.55) for i in range(3))
            out[x, y] = (col[0], col[1], col[2], alpha)

    mask = rgba.split()[3]
    # dilate the stroke mask so thin line-art survives small print sizes
    mask = mask.filter(ImageFilter.MaxFilter(3))
    # feather the mask edge so the paste has a smooth edge on the product
    mask = mask.filter(ImageFilter.GaussianBlur(0.8))
    rgba.putalpha(mask)

    return rgba.crop(rgba.getbbox())


def design_source(slug):
    """Resolve the artwork path for a design slug."""
    path = os.path.join(DESIGN_FOLDER, f"{slug}.png")
    if os.path.exists(path):
        return path
    return os.path.join(TSHIRTS_FOLDER, slug, TSHIRT_ART.get(slug, f"{slug}_terracotta.png"))


def fit(design, box):
    """Resize the design to fit inside `box` (w, h), preserving aspect ratio."""
    dw, dh = design.size
    scale = min(box[0] / dw, box[1] / dh)
    nw, nh = max(1, int(dw * scale)), max(1, int(dh * scale))
    return design.resize((nw, nh), Image.LANCZOS)


def paste_centered(canvas, design, center, box):
    """Paste `design` centred at `center` (x, y), scaled to fit `box`."""
    art = fit(design, box)
    canvas.paste(art, (center[0] - art.size[0] // 2, center[1] - art.size[1] // 2), art)


def soft_shadow(mask):
    """Blurred drop shadow from a binary mask (L mode, 0/255)."""
    shadow = mask.filter(ImageFilter.GaussianBlur(14))
    rgba = Image.new("RGBA", mask.size, (0, 0, 0, 0))
    rgba.putalpha(shadow.point(lambda v: int(v * 0.35)))
    return rgba


def canvas_with(design, draw_fn):
    """Fresh 800x800 canvas with warm gradient bg, then product drawn by draw_fn."""
    canvas = vgrad((SIZE, SIZE), (248, 243, 232), (253, 250, 243)).convert("RGBA")
    draw_fn(canvas, ImageDraw.Draw(canvas), design)
    return canvas


def draw_poster(canvas, d, design):
    cx = SIZE // 2
    w, h = 470, 600
    x0, y0 = cx - w // 2, 90
    # shadow
    sh = Image.new("L", (w + 40, h + 40), 0)
    ImageDraw.Draw(sh).rounded_rectangle([20, 24, 20 + w, 24 + h], 10, fill=255)
    canvas.paste(soft_shadow(sh), (x0 - 20, y0 - 24), soft_shadow(sh))
    # poster board
    d.rounded_rectangle([x0, y0, x0 + w, y0 + h], 10, fill=(255, 255, 255), outline=(226, 219, 202), width=3)
    # inner frame
    d.rectangle([x0 + 14, y0 + 14, x0 + w - 14, y0 + h - 14], outline=(238, 233, 222), width=2)
    paste_centered(canvas, design, (cx, y0 + 300), (360, 360))


def draw_sticker(canvas, d, design):
    w, h = 460, 460
    x0, y0 = (SIZE - w) // 2, 170
    # shadow
    sh = Image.new("L", (w + 40, h + 40), 0)
    ImageDraw.Draw(sh).rounded_rectangle([24, 28, 24 + w, 28 + h], 48, fill=255)
    canvas.paste(soft_shadow(sh), (x0 - 24 + 12, y0 - 28 + 14), soft_shadow(sh))
    # die-cut sticker with white border
    d.rounded_rectangle([x0, y0, x0 + w, y0 + h], 48, fill=(255, 255, 255))
    paste_centered(canvas, design, (SIZE // 2, y0 + h // 2), (360, 360))
    d.rounded_rectangle([x0 + 6, y0 + 6, x0 + w - 6, y0 + h - 6], 44, outline=(230, 225, 214), width=2)


def draw_notebook(canvas, d, design):
    w, h = 410, 540
    x0, y0 = (SIZE - w) // 2 + 18, 130
    sh = Image.new("L", (w + 40, h + 40), 0)
    ImageDraw.Draw(sh).rounded_rectangle([24, 26, 24 + w, 26 + h], 14, fill=255)
    canvas.paste(soft_shadow(sh), (x0 - 24 + 10, y0 - 26 + 12), soft_shadow(sh))
    # page block (right + bottom edge)
    d.rounded_rectangle([x0 + 10, y0 + 10, x0 + w + 10, y0 + h + 10], 12, fill=(255, 255, 255))
    # cover
    d.rounded_rectangle([x0, y0, x0 + w, y0 + h], 12, fill=(244, 239, 228), outline=(221, 213, 196), width=2)
    # spine
    d.rounded_rectangle([x0, y0, x0 + 34, y0 + h], 12, fill=(230, 222, 207))
    paste_centered(canvas, design, (x0 + w // 2 + 14, y0 + 240), (300, 300))


def draw_mug(canvas, d, design):
    cx = SIZE // 2
    body_w, body_h = 330, 360
    x0, y0 = cx - body_w // 2, 210
    # shadow under mug
    d.ellipse([cx - 150, 600, cx + 150, 640], fill=(0, 0, 0, 30))
    # handle (right)
    hx = x0 + body_w - 6
    d.arc([hx - 10, y0 + 70, hx + 110, y0 + 250], -60, 60, fill=(238, 233, 220), width=46)
    d.arc([hx - 10, y0 + 70, hx + 110, y0 + 250], -60, 60, fill=(250, 247, 238), width=30)
    # body (vertical gradient + rounded)
    body = Image.new("RGBA", (body_w, body_h))
    bd = ImageDraw.Draw(body)
    for i, x in enumerate(range(body_w)):
        t = x / (body_w - 1)
        col = tuple(int(252 - (t - 0.5) * 2 * 30) for _ in range(3))
        bd.line([(x, 0), (x, body_h)], fill=col + (255,))
    mask = Image.new("L", (body_w, body_h), 0)
    ImageDraw.Draw(mask).rounded_rectangle([0, 0, body_w - 1, body_h - 1], 46, fill=255)
    canvas.paste(body, (x0, y0), mask)
    # rim
    d.ellipse([x0 + 10, y0 - 14, x0 + body_w - 10, y0 + 26], fill=(252, 250, 244), outline=(228, 222, 210), width=3)
    # print
    paste_centered(canvas, design, (cx, y0 + body_h // 2 + 4), (240, 250))


def draw_tote(canvas, d, design):
    cx = SIZE // 2
    w, h = 380, 440
    x0, y0 = cx - w // 2, 230
    d.ellipse([cx - 160, 690, cx + 160, 726], fill=(0, 0, 0, 28))
    # handles
    for ox in (-90, 90):
        d.arc([cx + ox - 70, y0 - 140, cx + ox + 70, y0 + 30], 180, 360, fill=(214, 203, 178), width=26)
    # bag body
    d.rounded_rectangle([x0, y0, x0 + w, y0 + h], 22, fill=(236, 227, 207), outline=(210, 198, 172), width=3)
    # hem
    d.rounded_rectangle([x0, y0 + h - 46, x0 + w, y0 + h], 22, fill=(224, 213, 190))
    paste_centered(canvas, design, (cx, y0 + h // 2 - 20), (280, 290))


def draw_hoodie(canvas, d, design):
    cx = SIZE // 2
    body_w, body_h = 460, 500
    x0, y0 = cx - body_w // 2, 170
    d.ellipse([cx - 170, 690, cx + 170, 726], fill=(0, 0, 0, 28))
    # sleeves
    d.rounded_rectangle([x0 - 96, y0 + 60, x0 + 16, y0 + 330], 44, fill=(224, 219, 209))
    d.rounded_rectangle([x0 + body_w - 16, y0 + 60, x0 + body_w + 96, y0 + 330], 44, fill=(224, 219, 209))
    # body
    d.rounded_rectangle([x0, y0, x0 + body_w, y0 + body_h], 58, fill=(233, 229, 220))
    # hood behind neck
    d.pieslice([x0 + 90, y0 - 70, x0 + body_w - 90, y0 + 130], 180, 360, fill=(219, 214, 203))
    d.pieslice([x0 + 96, y0 - 60, x0 + body_w - 96, y0 + 118], 180, 360, fill=(219, 214, 203))
    # drawstrings
    d.line([(cx - 24, y0 + 74), (cx - 30, y0 + 118)], fill=(180, 172, 158), width=7)
    d.line([(cx + 24, y0 + 74), (cx + 30, y0 + 118)], fill=(180, 172, 158), width=7)
    d.ellipse([cx - 38, y0 + 112, cx - 22, y0 + 128], fill=(180, 172, 158))
    d.ellipse([cx + 22, y0 + 112, cx + 38, y0 + 128], fill=(180, 172, 158))
    # kangaroo pocket
    d.rounded_rectangle([x0 + 120, y0 + 300, x0 + body_w - 120, y0 + 420], 34, fill=(224, 219, 209))
    # ribbed hem
    d.rounded_rectangle([x0, y0 + body_h - 64, x0 + body_w, y0 + body_h], 30, fill=(217, 211, 200))
    # print on chest
    paste_centered(canvas, design, (cx, y0 + 210), (250, 250))


def draw_cap(canvas, d, design):
    cx = SIZE // 2
    w, h = 430, 300
    x0, y0 = cx - w // 2, 250
    d.ellipse([cx - 150, 560, cx + 150, 596], fill=(0, 0, 0, 28))
    # crown (dome)
    d.pieslice([x0, y0, x0 + w, y0 + h * 2], 180, 360, fill=(226, 221, 209))
    # panel seams
    for i in range(1, 4):
        sx = x0 + w * i / 4
        d.arc([sx - w / 8, y0 - 4, sx + w / 8, y0 + h * 2], 180, 360, fill=(208, 201, 187), width=3)
    # button
    d.ellipse([cx - 16, y0 - 16, cx + 16, y0 + 16], fill=(214, 207, 194))
    # brim (front)
    d.pieslice([x0 - 30, y0 + h - 60, x0 + w + 30, y0 + h + 150], 200, 340, fill=(240, 236, 226))
    d.pieslice([x0 - 24, y0 + h - 52, x0 + w + 24, y0 + h + 140], 205, 335, fill=(214, 207, 194))
    # print on front panel
    paste_centered(canvas, design, (cx, y0 + 130), (180, 180))


DRAWERS = {
    "hoodie": draw_hoodie,
    "mug": draw_mug,
    "tote-bag": draw_tote,
    "poster": draw_poster,
    "sticker-pack": draw_sticker,
    "notebook": draw_notebook,
    "cap": draw_cap,
}


def main():
    os.makedirs(OUT_DIR, exist_ok=True)
    designs = {}
    for slug in DESIGNS:
        path = design_source(slug)
        if not os.path.exists(path):
            print(f"  ! missing artwork: {path}")
            continue
        designs[slug] = extract_design(path)

    for category, drawer in DRAWERS.items():
        cat_dir = os.path.join(OUT_DIR, category)
        os.makedirs(cat_dir, exist_ok=True)
        for slug, design in designs.items():
            out = os.path.join(cat_dir, f"{slug}.png")
            canvas = canvas_with(design, drawer)
            canvas.convert("RGB").save(out, "PNG", optimize=True)
            print(f"  ✓ {category}/{slug}.png")

    print(f"Done — mockups written to {OUT_DIR}")


if __name__ == "__main__":
    main()
