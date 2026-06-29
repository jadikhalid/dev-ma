<?php

return [
    [
        'slug' => 'healthcare',
        'name_fr' => 'Corps soignant',
        'name_en' => 'Healthcare',
        'professions' => [
            [
                'slug' => 'nurse',
                'name_fr' => 'Infirmier(ère)',
                'name_en' => 'Nurse',
                'suggestions' => [
                    ['label_fr' => 'Infirmier(ère) bloc opératoire', 'label_en' => 'Operating room nurse', 'keywords' => 'bloc salle operation'],
                    ['label_fr' => 'Infirmier(ère) en soins intensifs', 'label_en' => 'ICU nurse', 'keywords' => 'reanimation soins intensifs'],
                    ['label_fr' => 'Infirmier(ère) pédiatrique', 'label_en' => 'Pediatric nurse', 'keywords' => 'pediatrie enfants'],
                    ['label_fr' => 'Infirmier(ère) à domicile', 'label_en' => 'Home care nurse', 'keywords' => 'had domicile'],
                    ['label_fr' => 'Infirmier(ère) coordinateur(trice)', 'label_en' => 'Nurse coordinator', 'keywords' => 'coordination'],
                ],
            ],
            [
                'slug' => 'doctor',
                'name_fr' => 'Médecin',
                'name_en' => 'Doctor',
                'suggestions' => [
                    ['label_fr' => 'Médecin généraliste', 'label_en' => 'General practitioner', 'keywords' => 'generaliste'],
                    ['label_fr' => 'Médecin urgentiste', 'label_en' => 'Emergency physician', 'keywords' => 'urgences'],
                    ['label_fr' => 'Radiologue', 'label_en' => 'Radiologist', 'keywords' => 'imagerie radio'],
                    ['label_fr' => 'Anesthésiste-réanimateur', 'label_en' => 'Anesthesiologist', 'keywords' => 'anesthesie'],
                ],
            ],
            [
                'slug' => 'care-assistant',
                'name_fr' => 'Aide-soignant(e)',
                'name_en' => 'Care assistant',
                'suggestions' => [
                    ['label_fr' => 'Aide-soignant(e) gériatrie', 'label_en' => 'Geriatric care assistant', 'keywords' => 'ehpad personnes agees'],
                    ['label_fr' => 'Aide-soignant(e) à domicile', 'label_en' => 'Home care assistant', 'keywords' => 'had domicile'],
                ],
            ],
            [
                'slug' => 'physiotherapist',
                'name_fr' => 'Kinésithérapeute',
                'name_en' => 'Physiotherapist',
                'suggestions' => [
                    ['label_fr' => 'Kinésithérapeute sportif', 'label_en' => 'Sports physiotherapist', 'keywords' => 'sport reeducation'],
                    ['label_fr' => 'Kinésithérapeute rééducation', 'label_en' => 'Rehabilitation physiotherapist', 'keywords' => 'reeducation fonctionnelle'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'it-digital',
        'name_fr' => 'Informatique & digital',
        'name_en' => 'IT & Digital',
        'professions' => [
            [
                'slug' => 'web-developer',
                'name_fr' => 'Développeur web',
                'name_en' => 'Web developer',
                'suggestions' => [
                    ['label_fr' => 'Développeur full-stack', 'label_en' => 'Full-stack developer', 'keywords' => 'fullstack full stack'],
                    ['label_fr' => 'Développeur frontend', 'label_en' => 'Frontend developer', 'keywords' => 'front-end ui'],
                    ['label_fr' => 'Développeur backend', 'label_en' => 'Backend developer', 'keywords' => 'back-end api'],
                    ['label_fr' => 'Laravel', 'label_en' => 'Laravel', 'keywords' => 'php framework'],
                    ['label_fr' => 'React', 'label_en' => 'React', 'keywords' => 'javascript frontend js'],
                    ['label_fr' => 'Vue.js', 'label_en' => 'Vue.js', 'keywords' => 'javascript frontend js'],
                    ['label_fr' => 'Next.js', 'label_en' => 'Next.js', 'keywords' => 'react javascript frontend'],
                    ['label_fr' => 'Node.js', 'label_en' => 'Node.js', 'keywords' => 'javascript backend api'],
                    ['label_fr' => 'PHP', 'label_en' => 'PHP', 'keywords' => 'symfony laravel backend'],
                    ['label_fr' => 'Python', 'label_en' => 'Python', 'keywords' => 'django flask backend'],
                    ['label_fr' => 'DevOps', 'label_en' => 'DevOps', 'keywords' => 'ci cd infrastructure docker'],
                ],
            ],
            [
                'slug' => 'mobile-developer',
                'name_fr' => 'Développeur mobile',
                'name_en' => 'Mobile developer',
                'suggestions' => [
                    ['label_fr' => 'React Native', 'label_en' => 'React Native', 'keywords' => 'mobile javascript'],
                    ['label_fr' => 'Flutter', 'label_en' => 'Flutter', 'keywords' => 'mobile dart'],
                    ['label_fr' => 'Développeur iOS', 'label_en' => 'iOS developer', 'keywords' => 'swift apple'],
                    ['label_fr' => 'Développeur Android', 'label_en' => 'Android developer', 'keywords' => 'kotlin java'],
                ],
            ],
            [
                'slug' => 'data-specialist',
                'name_fr' => 'Data & BI',
                'name_en' => 'Data & BI',
                'suggestions' => [
                    ['label_fr' => 'Data analyst', 'label_en' => 'Data analyst', 'keywords' => 'analyse donnees'],
                    ['label_fr' => 'Data engineer', 'label_en' => 'Data engineer', 'keywords' => 'pipeline etl'],
                    ['label_fr' => 'Business Intelligence', 'label_en' => 'Business Intelligence', 'keywords' => 'bi reporting power bi'],
                ],
            ],
            [
                'slug' => 'designer',
                'name_fr' => 'Designer',
                'name_en' => 'Designer',
                'suggestions' => [
                    ['label_fr' => 'UI/UX Designer', 'label_en' => 'UI/UX Designer', 'keywords' => 'design figma'],
                    ['label_fr' => 'Product Designer', 'label_en' => 'Product Designer', 'keywords' => 'ux produit'],
                ],
            ],
            [
                'slug' => 'cybersecurity',
                'name_fr' => 'Cybersécurité',
                'name_en' => 'Cybersecurity',
                'suggestions' => [
                    ['label_fr' => 'Analyste SOC', 'label_en' => 'SOC analyst', 'keywords' => 'securite monitoring'],
                    ['label_fr' => 'Pentester', 'label_en' => 'Penetration tester', 'keywords' => 'audit securite'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'technicians',
        'name_fr' => 'Techniciens & BTP',
        'name_en' => 'Technicians & Construction',
        'professions' => [
            [
                'slug' => 'electrician',
                'name_fr' => 'Électricien',
                'name_en' => 'Electrician',
                'suggestions' => [
                    ['label_fr' => 'Électricien bâtiment', 'label_en' => 'Building electrician', 'keywords' => 'btp construction'],
                    ['label_fr' => 'Électricien industriel', 'label_en' => 'Industrial electrician', 'keywords' => 'industrie maintenance'],
                    ['label_fr' => 'Électricien maintenance', 'label_en' => 'Maintenance electrician', 'keywords' => 'depannage'],
                ],
            ],
            [
                'slug' => 'hvac-technician',
                'name_fr' => 'Technicien HVAC',
                'name_en' => 'HVAC technician',
                'suggestions' => [
                    ['label_fr' => 'Technicien climatisation', 'label_en' => 'Air conditioning technician', 'keywords' => 'clim froid'],
                    ['label_fr' => 'Technicien froid industriel', 'label_en' => 'Industrial refrigeration technician', 'keywords' => 'froid industriel'],
                ],
            ],
            [
                'slug' => 'mechanic',
                'name_fr' => 'Mécanicien',
                'name_en' => 'Mechanic',
                'suggestions' => [
                    ['label_fr' => 'Mécanicien automobile', 'label_en' => 'Automotive mechanic', 'keywords' => 'auto voiture'],
                    ['label_fr' => 'Mécanicien industriel', 'label_en' => 'Industrial mechanic', 'keywords' => 'maintenance machines'],
                ],
            ],
            [
                'slug' => 'plumber',
                'name_fr' => 'Plombier',
                'name_en' => 'Plumber',
                'suggestions' => [
                    ['label_fr' => 'Plombier bâtiment', 'label_en' => 'Building plumber', 'keywords' => 'sanitaire btp'],
                    ['label_fr' => 'Plombier maintenance', 'label_en' => 'Maintenance plumber', 'keywords' => 'depannage'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'finance',
        'name_fr' => 'Finance & gestion',
        'name_en' => 'Finance & Management',
        'professions' => [
            [
                'slug' => 'accountant',
                'name_fr' => 'Comptable',
                'name_en' => 'Accountant',
                'suggestions' => [
                    ['label_fr' => 'Comptable général', 'label_en' => 'General accountant', 'keywords' => 'comptabilite'],
                    ['label_fr' => 'Expert-comptable', 'label_en' => 'Chartered accountant', 'keywords' => 'audit expertise'],
                    ['label_fr' => 'Gestionnaire de paie', 'label_en' => 'Payroll manager', 'keywords' => 'paie rh'],
                ],
            ],
            [
                'slug' => 'financial-controller',
                'name_fr' => 'Contrôleur de gestion',
                'name_en' => 'Financial controller',
                'suggestions' => [
                    ['label_fr' => 'Contrôleur de gestion', 'label_en' => 'Management controller', 'keywords' => 'reporting'],
                    ['label_fr' => 'Analyste FP&A', 'label_en' => 'FP&A analyst', 'keywords' => 'budget previsionnel'],
                ],
            ],
            [
                'slug' => 'cfo',
                'name_fr' => 'DAF / Finance',
                'name_en' => 'CFO / Finance',
                'suggestions' => [
                    ['label_fr' => 'Directeur administratif et financier', 'label_en' => 'Chief financial officer', 'keywords' => 'daf finance'],
                    ['label_fr' => 'Trésorier', 'label_en' => 'Treasurer', 'keywords' => 'tresorerie cash'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'hospitality',
        'name_fr' => 'Hôtellerie & restauration',
        'name_en' => 'Hospitality & Catering',
        'professions' => [
            [
                'slug' => 'chef',
                'name_fr' => 'Cuisinier(ère) / Chef',
                'name_en' => 'Chef / Cook',
                'suggestions' => [
                    ['label_fr' => 'Chef de cuisine', 'label_en' => 'Head chef', 'keywords' => 'cuisine restaurant'],
                    ['label_fr' => 'Chef de partie', 'label_en' => 'Station chef', 'keywords' => 'brigade cuisine'],
                    ['label_fr' => 'Pâtissier(ère)', 'label_en' => 'Pastry chef', 'keywords' => 'patisserie dessert'],
                ],
            ],
            [
                'slug' => 'hotel-staff',
                'name_fr' => 'Hôtellerie',
                'name_en' => 'Hotel staff',
                'suggestions' => [
                    ['label_fr' => 'Réceptionniste hôtel', 'label_en' => 'Hotel receptionist', 'keywords' => 'accueil hotel'],
                    ['label_fr' => 'Gouvernant(e) d\'étages', 'label_en' => 'Housekeeping manager', 'keywords' => 'menage etages'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'industry',
        'name_fr' => 'Industrie & maintenance',
        'name_en' => 'Industry & Maintenance',
        'professions' => [
            [
                'slug' => 'maintenance-technician',
                'name_fr' => 'Technicien de maintenance',
                'name_en' => 'Maintenance technician',
                'suggestions' => [
                    ['label_fr' => 'Technicien maintenance industrielle', 'label_en' => 'Industrial maintenance technician', 'keywords' => 'usine production'],
                    ['label_fr' => 'Technicien multibranche', 'label_en' => 'Multi-skilled technician', 'keywords' => 'polyvalent'],
                ],
            ],
            [
                'slug' => 'quality-technician',
                'name_fr' => 'Qualité & HSE',
                'name_en' => 'Quality & HSE',
                'suggestions' => [
                    ['label_fr' => 'Technicien qualité', 'label_en' => 'Quality technician', 'keywords' => 'qse qualite'],
                    ['label_fr' => 'Responsable HSE', 'label_en' => 'HSE manager', 'keywords' => 'securite environnement'],
                ],
            ],
        ],
    ],
];
