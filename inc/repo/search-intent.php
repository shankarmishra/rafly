<?php
/**
 * RAFly Agency OS — Search Intent & Ground-Truth Entity Classification Engine.
 *
 * Maps search queries to canonical URLs, classifies intent (Commercial, Local, Technical, Informational),
 * and maintains verified capability entity taxonomies.
 */

/** Verified technology and service capability entity matrix. */
function search_intent_entity_matrix(): array {
    return [
        'web-development' => [
            'frontend' => [
                ['name' => 'React', 'status' => 'VERIFIED'],
                ['name' => 'Next.js', 'status' => 'VERIFIED'],
                ['name' => 'JavaScript', 'status' => 'VERIFIED'],
                ['name' => 'TypeScript', 'status' => 'VERIFIED'],
            ],
            'backend' => [
                ['name' => 'PHP', 'status' => 'VERIFIED'],
                ['name' => 'Node.js', 'status' => 'VERIFIED'],
                ['name' => 'Python', 'status' => 'VERIFIED'],
                ['name' => 'Laravel', 'status' => 'CANDIDATE'],
                ['name' => 'Django', 'status' => 'CANDIDATE'],
            ],
            'databases' => [
                ['name' => 'MySQL', 'status' => 'VERIFIED'],
                ['name' => 'PostgreSQL', 'status' => 'VERIFIED'],
            ],
        ],
        'app-development' => [
            'mobile_frameworks' => [
                ['name' => 'React Native', 'status' => 'VERIFIED'],
                ['name' => 'Flutter', 'status' => 'VERIFIED'],
                ['name' => 'Android (Kotlin)', 'status' => 'VERIFIED'],
                ['name' => 'iOS (Swift)', 'status' => 'VERIFIED'],
            ],
        ],
        'web-security' => [
            'standards' => [
                ['name' => 'OWASP Top 10', 'status' => 'VERIFIED'],
                ['name' => 'CSP Hardening', 'status' => 'VERIFIED'],
                ['name' => 'WAF Protection', 'status' => 'VERIFIED'],
                ['name' => 'Argon2id Hashing', 'status' => 'VERIFIED'],
            ],
        ],
        'ecommerce' => [
            'platforms' => [
                ['name' => 'Shopify', 'status' => 'VERIFIED'],
                ['name' => 'WooCommerce', 'status' => 'VERIFIED'],
                ['name' => 'Custom PHP Engine', 'status' => 'VERIFIED'],
            ],
        ],
        'lead-automation' => [
            'channels' => [
                ['name' => 'WhatsApp API', 'status' => 'VERIFIED'],
                ['name' => 'CRM Lead Routing', 'status' => 'VERIFIED'],
                ['name' => 'Zapier / Webhooks', 'status' => 'VERIFIED'],
            ],
        ],
        'performance-marketing' => [
            'platforms' => [
                ['name' => 'Google Ads', 'status' => 'VERIFIED'],
                ['name' => 'Meta Ads', 'status' => 'VERIFIED'],
                ['name' => 'GA4 & GTM', 'status' => 'VERIFIED'],
            ],
        ],
        'content-creation' => [
            'pillars' => [
                ['name' => 'Technical Copywriting', 'status' => 'VERIFIED'],
                ['name' => 'Video Production', 'status' => 'VERIFIED'],
                ['name' => 'Brand Storytelling', 'status' => 'VERIFIED'],
            ],
        ],
    ];
}

/** Resolves a search query to its target canonical page and intent. */
function search_intent_resolve(string $query): array {
    $q = strtolower(trim($query));
    $norm = preg_replace('/[^a-z0-9\s]/', '', $q);

    // Intent Mapping Rules
    if (str_contains($norm, 'noida') || str_contains($norm, 'delhi') || str_contains($norm, 'mumbai') || str_contains($norm, 'dubai') || str_contains($norm, 'gurgaon')) {
        $intent = 'Local Commercial';
    } elseif (str_contains($norm, 'company') || str_contains($norm, 'agency') || str_contains($norm, 'best') || str_contains($norm, 'hire')) {
        $intent = 'Commercial High-Intent';
    } elseif (str_contains($norm, 'vs') || str_contains($norm, 'guide') || str_contains($norm, 'architecture') || str_contains($norm, 'tutorial')) {
        $intent = 'Technical Informational';
    } else {
        $intent = 'General Navigational';
    }

    // Match Service / Resource Canonical
    if (str_contains($norm, 'security') || str_contains($norm, 'audit') || str_contains($norm, 'owasp')) {
        $target = '/services/web-security';
    } elseif (str_contains($norm, 'app') || str_contains($norm, 'react native') || str_contains($norm, 'flutter') || str_contains($norm, 'android') || str_contains($norm, 'ios')) {
        if (str_contains($norm, 'vs') || str_contains($norm, 'architecture')) {
            $target = str_contains($norm, 'flutter') ? '/resources/flutter-app-development' : '/resources/react-native-app-development';
        } else {
            $target = '/services/app-development';
        }
    } elseif (str_contains($norm, 'shopify') || str_contains($norm, 'ecommerce') || str_contains($norm, 'woocommerce')) {
        $target = '/services/ecommerce';
    } elseif (str_contains($norm, 'whatsapp') || str_contains($norm, 'automation') || str_contains($norm, 'crm')) {
        $target = '/services/lead-automation';
    } elseif (str_contains($norm, 'ads') || str_contains($norm, 'marketing') || str_contains($norm, 'google ads')) {
        $target = '/services/performance-marketing';
    } elseif (str_contains($norm, 'content') || str_contains($norm, 'video') || str_contains($norm, 'copywriting')) {
        $target = '/services/content-creation';
    } else {
        $target = '/services/web-development';
    }

    // City Specific Override
    if (str_contains($norm, 'web development') && str_contains($norm, 'delhi')) {
        $target = '/services/web-development/delhi';
    } elseif (str_contains($norm, 'security') && str_contains($norm, 'noida')) {
        $target = '/services/web-security/noida';
    } elseif (str_contains($norm, 'ecommerce') && str_contains($norm, 'mumbai')) {
        $target = '/services/ecommerce/mumbai';
    }

    return [
        'query'          => $query,
        'normalized'     => $norm,
        'intent'         => $intent,
        'canonical_path' => $target,
        'canonical_url'  => 'https://rafly.in' . $target,
    ];
}
