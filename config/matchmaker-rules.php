<?php

return [
    'compliance' => [
        'title' => 'Fair Housing Act (FHA) & Regulatory Compliance',
        'rules' => [
            'fha_neutrality' => [
                'name' => 'Protected Class Neutrality',
                'action' => 'Scan and remove any direct or indirect references to race, color, religion, national origin, sex, sexual orientation, disability, or familial status.',
                'tooltip' => 'Audit failed: Detected terms that may violate local protected class guidelines. Keep focus strictly on physical property attributes.'
            ],
            'demographic_steering' => [
                'name' => 'Prohibited Demographic Steering',
                'action' => 'Flag language directing clients toward or away from specific neighborhoods based on demographic assumptions or family makeup.',
                'tooltip' => 'Avoid steering. Do not make assumptions about where a client would feel comfortable.'
            ],
            'restricted_descriptors' => [
                'name' => 'Restricted Neighborhood Descriptors',
                'action' => 'Block subjective descriptors like "family-friendly", "safe", "quiet", "low-crime", "diverse", "active community", or "ideal for retirees". Replace with objective, physical facts.',
                'tooltip' => 'Restricted Descriptor: Subjective descriptions carry high compliance risks. Replace with objective, spatial, or physical facts.'
            ],
            'demographic_restrictions' => [
                'name' => 'Target Demographic Restrictions',
                'action' => 'Eliminate preferred buyer/tenant profiles like "perfect for young professionals", "ideal for empty nesters", or "suitable for a single person".',
                'tooltip' => 'Do not describe the ideal occupant. Focus exclusively on describing the physical features of the real estate.'
            ],
        ]
    ],
    'quality' => [
        'title' => 'Copy Editing, Quality, & Professionalism',
        'rules' => [
            'luxury_vernacular' => [
                'name' => 'Elevate to Luxury Vernacular',
                'action' => 'Replace common adjectives (e.g. "nice kitchen", "big yard") with sophisticated real estate vocabulary ("gourmet kitchen", "expansive grounds"). Avoid marketing hyperbole.',
                'tooltip' => 'Elevate vocabulary: Replace basic descriptors with refined, professional terminology suitable for high-value transactions.'
            ],
            'active_voice' => [
                'name' => 'Active Voice Enforcement',
                'action' => 'Identify and restructure passive sentence constructions into active, assertive statements.',
                'tooltip' => 'Passive voice detected. Rephrase to make the subject perform the action to improve executive tone.'
            ],
            'visual_hierarchy' => [
                'name' => 'Visual Hierarchy & Readability',
                'action' => 'Ensure email paragraphs do not exceed three sentences. Use bullet points for structural lists.',
                'tooltip' => 'Break up dense text. Limit paragraphs to 3 sentences and use structured bullets for mobile readability.'
            ],
            'objective_clarity' => [
                'name' => 'Objective Clarity over Fluff',
                'action' => 'Strip out excessive exclamation points, decorative emojis, and vague marketing filler. Keep punctuation professional.',
                'tooltip' => 'Remove conversational clutter. Maintain a polished, professional written presence.'
            ],
        ]
    ],
    'context' => [
        'title' => 'Contextual Alignment & Conversation Flow',
        'rules' => [
            'query_resolution' => [
                'name' => 'Historical Query Resolution',
                'action' => 'Cross-reference the draft against the client past inquiries (e.g., budget constraints, specific locations) to confirm every explicit question has a precise, direct answer.',
                'tooltip' => 'Context check: Verify that this draft directly answers the specific questions raised in the client past email.'
            ],
            'redundancy_suppression' => [
                'name' => 'Redundancy and Greeting Suppression',
                'action' => 'Scan and delete repetitive pleasantries, greetings, or historical recaps.',
                'tooltip' => 'Draft contains redundant pleasantries. Condense the text to respect the client time.'
            ],
            'robotic_elimination' => [
                'name' => 'Robotic Phrase Elimination',
                'action' => 'Flag and replace formulaic templates or defensive corporate phrasing (e.g., "Please find attached") with natural, advisory phrasing.',
                'tooltip' => 'Template phrasing detected. Rephrase to ensure the tone is warm, bespoke, and advisory rather than transactional.'
            ],
        ]
    ]
];
