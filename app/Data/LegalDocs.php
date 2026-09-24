<?php

namespace App\Data;

/**
 * Privacy Policy and Terms of Service copy.
 *
 * Ported verbatim from the React front-end's LegalPage component, which held
 * both documents inline. Keeping them in data keeps the Blade view shared.
 */
class LegalDocs
{
    /**
     * All documents, keyed by their URL segment.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'privacy' => self::privacy(),
            'terms' => self::terms(),
        ];
    }

    /**
     * A single document, or null when the slug is unknown.
     *
     * @return array<string, mixed>|null
     */
    public static function get(string $doc): ?array
    {
        return self::all()[$doc] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    protected static function privacy(): array
    {
        return [
            'slug' => 'privacy',
            'title' => 'Privacy Policy',
            'updated' => 'Last updated: August 2026',
            'intro' => "Pakka Patriot ('we', 'us') is committed to protecting the privacy of every child, parent, and visitor to this website. This policy explains what we collect, why we collect it, and how you stay in control.",
            'sections' => [
                [
                    'heading' => 'Information we collect',
                    'body' => [
                        'Contact details you choose to share — such as your name and email address when you join the journey, subscribe to our newsletter, or place an order.',
                        'Basic usage information (pages visited, device type) collected anonymously to help us understand what content our community loves.',
                    ],
                ],
                [
                    'heading' => 'How we use your information',
                    'body' => [
                        'To send the stories, eBooks, and updates you asked for, and to deliver merchandise orders placed through our store.',
                        'To improve our content and website experience. We never sell your personal information to anyone.',
                    ],
                ],
                [
                    'heading' => "Children's privacy",
                    'body' => [
                        'Pakka Patriot is designed for curious young minds. We encourage parents and guardians to explore the site together with their children and to supervise any information shared in forms.',
                    ],
                ],
                [
                    'heading' => 'Your choices',
                    'body' => [
                        'You may unsubscribe from emails at any time using the link in any email, or contact us to request access to, correction of, or deletion of the information you have shared with us.',
                    ],
                ],
                [
                    'heading' => 'Contact us',
                    'body' => [
                        'Questions about this policy? Write to us at support@pakkapatriot.com and we will be happy to help.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function terms(): array
    {
        return [
            'slug' => 'terms',
            'title' => 'Terms of Service',
            'updated' => 'Last updated: August 2026',
            'intro' => 'Welcome to Pakka Patriot! These terms keep the experience safe, fair, and fun for everyone — kids, parents, teachers, and fellow patriots.',
            'sections' => [
                [
                    'heading' => 'Using the website',
                    'body' => [
                        'All content on Pakka Patriot — stories, eBooks, games, and ideas — is provided for personal, non-commercial, educational use. Feel free to learn, explore, and share the inspiration with credit to Pakka Patriot.',
                    ],
                ],
                [
                    'heading' => 'eBooks & digital content',
                    'body' => [
                        'Our free eBook library is offered for personal reading and classroom use. Please do not resell or republish the digital content without our permission.',
                    ],
                ],
                [
                    'heading' => 'Merchandise orders',
                    'body' => [
                        'Orders placed through our store are recorded and fulfilled by the Pakka Patriot team. We will reach out to confirm payment and delivery details. Prices are shown in Indian Rupees (₹) and may change from time to time.',
                    ],
                ],
                [
                    'heading' => 'Games',
                    'body' => [
                        'Our traditional Indian games (Chaukabaara, Pachisi, Aadu Puli Aatam, and friends) are free to play. Online rooms are for friendly, respectful play — be a good sport, always.',
                    ],
                ],
                [
                    'heading' => 'Acceptable use',
                    'body' => [
                        'Please treat fellow community members with respect. Do not misuse the site, attempt to disrupt its services, or share harmful content.',
                    ],
                ],
                [
                    'heading' => 'Changes & contact',
                    'body' => [
                        'We may update these terms as the site grows. Continued use of the website means you accept the current terms. Questions? Write to us at support@pakkapatriot.com.',
                    ],
                ],
            ],
        ];
    }
}
