<?php

/**
 * Catalogue métiers — secteurs d’activité économiques (réf. NACE / pratiques RH FR-MA),
 * puis métiers, puis spécialisations / mots-clés.
 */
return [
    [
        'slug' => 'health-social',
        'name_fr' => 'Santé & médico-social',
        'name_en' => 'Health & social care',
        'professions' => [
            [
                'slug' => 'nurse',
                'name_fr' => 'Infirmier(ère)',
                'name_en' => 'Nurse',
                'suggestions' => [
                    ['label_fr' => 'Infirmier(ère) bloc opératoire', 'label_en' => 'Operating room nurse', 'keywords' => 'bloc salle operation ibode'],
                    ['label_fr' => 'Infirmier(ère) soins intensifs', 'label_en' => 'ICU nurse', 'keywords' => 'reanimation soins intensifs urgence'],
                    ['label_fr' => 'Infirmier(ère) pédiatrique', 'label_en' => 'Pediatric nurse', 'keywords' => 'pediatrie enfants'],
                    ['label_fr' => 'Infirmier(ère) à domicile', 'label_en' => 'Home care nurse', 'keywords' => 'had domicile libéral'],
                    ['label_fr' => 'Infirmier(ère) coordinateur(trice)', 'label_en' => 'Nurse coordinator', 'keywords' => 'coordination parcours'],
                ],
            ],
            [
                'slug' => 'doctor',
                'name_fr' => 'Médecin',
                'name_en' => 'Doctor',
                'suggestions' => [
                    ['label_fr' => 'Médecin généraliste', 'label_en' => 'General practitioner', 'keywords' => 'generaliste medecine'],
                    ['label_fr' => 'Médecin urgentiste', 'label_en' => 'Emergency physician', 'keywords' => 'urgences samu'],
                    ['label_fr' => 'Radiologue', 'label_en' => 'Radiologist', 'keywords' => 'imagerie radio irm'],
                    ['label_fr' => 'Anesthésiste-réanimateur', 'label_en' => 'Anesthesiologist', 'keywords' => 'anesthesie reanimation'],
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
            [
                'slug' => 'pharmacist',
                'name_fr' => 'Pharmacien(ne)',
                'name_en' => 'Pharmacist',
                'suggestions' => [
                    ['label_fr' => 'Pharmacien d\'officine', 'label_en' => 'Community pharmacist', 'keywords' => 'officine medicaments'],
                    ['label_fr' => 'Pharmacien hospitalier', 'label_en' => 'Hospital pharmacist', 'keywords' => 'hopital pharmacie'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'it-digital',
        'name_fr' => 'Technologies de l\'information',
        'name_en' => 'Information technology',
        'professions' => [
            [
                'slug' => 'web-developer',
                'name_fr' => 'Développeur web',
                'name_en' => 'Web developer',
                'suggestions' => [
                    ['label_fr' => 'Développeur full-stack', 'label_en' => 'Full-stack developer', 'keywords' => 'fullstack full stack'],
                    ['label_fr' => 'Développeur frontend', 'label_en' => 'Frontend developer', 'keywords' => 'front-end ui react vue'],
                    ['label_fr' => 'Développeur backend', 'label_en' => 'Backend developer', 'keywords' => 'back-end api microservices'],
                    ['label_fr' => 'Laravel', 'label_en' => 'Laravel', 'keywords' => 'php framework'],
                    ['label_fr' => 'React', 'label_en' => 'React', 'keywords' => 'javascript frontend js'],
                    ['label_fr' => 'Vue.js', 'label_en' => 'Vue.js', 'keywords' => 'javascript frontend js'],
                    ['label_fr' => 'Next.js', 'label_en' => 'Next.js', 'keywords' => 'react javascript frontend'],
                    ['label_fr' => 'Node.js', 'label_en' => 'Node.js', 'keywords' => 'javascript backend api'],
                    ['label_fr' => 'Python', 'label_en' => 'Python', 'keywords' => 'django flask fastapi'],
                    ['label_fr' => 'DevOps', 'label_en' => 'DevOps', 'keywords' => 'ci cd docker kubernetes'],
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
                'name_fr' => 'Data & IA',
                'name_en' => 'Data & AI',
                'suggestions' => [
                    ['label_fr' => 'Data analyst', 'label_en' => 'Data analyst', 'keywords' => 'analyse donnees sql'],
                    ['label_fr' => 'Data engineer', 'label_en' => 'Data engineer', 'keywords' => 'pipeline etl spark'],
                    ['label_fr' => 'Data scientist', 'label_en' => 'Data scientist', 'keywords' => 'machine learning ia'],
                    ['label_fr' => 'Business Intelligence', 'label_en' => 'Business Intelligence', 'keywords' => 'bi power bi tableau'],
                ],
            ],
            [
                'slug' => 'designer',
                'name_fr' => 'Design produit & UX',
                'name_en' => 'Product & UX design',
                'suggestions' => [
                    ['label_fr' => 'UI/UX Designer', 'label_en' => 'UI/UX Designer', 'keywords' => 'figma design interface'],
                    ['label_fr' => 'Product Designer', 'label_en' => 'Product Designer', 'keywords' => 'ux produit research'],
                ],
            ],
            [
                'slug' => 'cybersecurity',
                'name_fr' => 'Cybersécurité',
                'name_en' => 'Cybersecurity',
                'suggestions' => [
                    ['label_fr' => 'Analyste SOC', 'label_en' => 'SOC analyst', 'keywords' => 'securite monitoring siem'],
                    ['label_fr' => 'Pentester', 'label_en' => 'Penetration tester', 'keywords' => 'audit securite pentest'],
                    ['label_fr' => 'RSS / GRC', 'label_en' => 'CISO / GRC', 'keywords' => 'conformite iso risque'],
                ],
            ],
            [
                'slug' => 'product-manager',
                'name_fr' => 'Product management',
                'name_en' => 'Product management',
                'suggestions' => [
                    ['label_fr' => 'Product Owner', 'label_en' => 'Product Owner', 'keywords' => 'agile scrum backlog'],
                    ['label_fr' => 'Product Manager', 'label_en' => 'Product Manager', 'keywords' => 'roadmap produit'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'construction',
        'name_fr' => 'BTP & construction',
        'name_en' => 'Construction & civil works',
        'professions' => [
            [
                'slug' => 'site-manager',
                'name_fr' => 'Conducteur de travaux',
                'name_en' => 'Site / works manager',
                'suggestions' => [
                    ['label_fr' => 'Conducteur de travaux bâtiment', 'label_en' => 'Building works manager', 'keywords' => 'chantier btp gestion'],
                    ['label_fr' => 'Conducteur de travaux VRD', 'label_en' => 'Civil works manager', 'keywords' => 'vrd infrastructure'],
                ],
            ],
            [
                'slug' => 'architect',
                'name_fr' => 'Architecte',
                'name_en' => 'Architect',
                'suggestions' => [
                    ['label_fr' => 'Architecte d\'intérieur', 'label_en' => 'Interior architect', 'keywords' => 'amenagement design'],
                    ['label_fr' => 'Architecte projet', 'label_en' => 'Project architect', 'keywords' => 'conception permis'],
                ],
            ],
            [
                'slug' => 'civil-engineer',
                'name_fr' => 'Ingénieur génie civil',
                'name_en' => 'Civil engineer',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur structure', 'label_en' => 'Structural engineer', 'keywords' => 'beton structure calcul'],
                    ['label_fr' => 'Ingénieur études BTP', 'label_en' => 'Construction design engineer', 'keywords' => 'etudes metrage'],
                ],
            ],
            [
                'slug' => 'electrician',
                'name_fr' => 'Électricien',
                'name_en' => 'Electrician',
                'suggestions' => [
                    ['label_fr' => 'Électricien bâtiment', 'label_en' => 'Building electrician', 'keywords' => 'btp courant faible'],
                    ['label_fr' => 'Électricien industriel', 'label_en' => 'Industrial electrician', 'keywords' => 'industrie tableaux'],
                    ['label_fr' => 'Électricien maintenance', 'label_en' => 'Maintenance electrician', 'keywords' => 'depannage'],
                ],
            ],
            [
                'slug' => 'plumber',
                'name_fr' => 'Plombier / CVC',
                'name_en' => 'Plumber / HVAC',
                'suggestions' => [
                    ['label_fr' => 'Plombier bâtiment', 'label_en' => 'Building plumber', 'keywords' => 'sanitaire'],
                    ['label_fr' => 'Technicien climatisation', 'label_en' => 'Air conditioning technician', 'keywords' => 'clim froid cvc'],
                    ['label_fr' => 'Technicien froid industriel', 'label_en' => 'Industrial refrigeration technician', 'keywords' => 'froid industriel'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'industry',
        'name_fr' => 'Industrie & production',
        'name_en' => 'Industry & manufacturing',
        'professions' => [
            [
                'slug' => 'maintenance-technician',
                'name_fr' => 'Technicien de maintenance',
                'name_en' => 'Maintenance technician',
                'suggestions' => [
                    ['label_fr' => 'Maintenance industrielle', 'label_en' => 'Industrial maintenance', 'keywords' => 'usine production TPM'],
                    ['label_fr' => 'Technicien multibranche', 'label_en' => 'Multi-skilled technician', 'keywords' => 'polyvalent electromecanique'],
                    ['label_fr' => 'Mécanicien industriel', 'label_en' => 'Industrial mechanic', 'keywords' => 'machines outils'],
                ],
            ],
            [
                'slug' => 'quality-technician',
                'name_fr' => 'Qualité, sécurité & environnement',
                'name_en' => 'Quality, safety & environment',
                'suggestions' => [
                    ['label_fr' => 'Technicien qualité', 'label_en' => 'Quality technician', 'keywords' => 'qse iso controle'],
                    ['label_fr' => 'Responsable HSE', 'label_en' => 'HSE manager', 'keywords' => 'securite environnement'],
                    ['label_fr' => 'Auditeur qualité', 'label_en' => 'Quality auditor', 'keywords' => 'audit certification'],
                ],
            ],
            [
                'slug' => 'production-manager',
                'name_fr' => 'Production & méthodes',
                'name_en' => 'Production & methods',
                'suggestions' => [
                    ['label_fr' => 'Responsable production', 'label_en' => 'Production manager', 'keywords' => 'atelier planning'],
                    ['label_fr' => 'Technicien méthodes', 'label_en' => 'Methods technician', 'keywords' => 'industrialisation lean'],
                ],
            ],
            [
                'slug' => 'mechanic',
                'name_fr' => 'Mécanicien automobile',
                'name_en' => 'Automotive mechanic',
                'suggestions' => [
                    ['label_fr' => 'Mécanicien automobile', 'label_en' => 'Automotive mechanic', 'keywords' => 'auto diagnostic'],
                    ['label_fr' => 'Électronicien auto', 'label_en' => 'Auto electronics technician', 'keywords' => 'diag embarque'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'energy',
        'name_fr' => 'Énergie & environnement',
        'name_en' => 'Energy & environment',
        'professions' => [
            [
                'slug' => 'energy-engineer',
                'name_fr' => 'Ingénieur énergie',
                'name_en' => 'Energy engineer',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur énergies renouvelables', 'label_en' => 'Renewable energy engineer', 'keywords' => 'solaire eolien photovoltaique'],
                    ['label_fr' => 'Ingénieur efficacité énergétique', 'label_en' => 'Energy efficiency engineer', 'keywords' => 'audit energetique'],
                ],
            ],
            [
                'slug' => 'hvac-technician',
                'name_fr' => 'Technicien fluides & réseaux',
                'name_en' => 'Fluids & networks technician',
                'suggestions' => [
                    ['label_fr' => 'Technicien réseaux électriques', 'label_en' => 'Electrical networks technician', 'keywords' => 'HTA distribution'],
                    ['label_fr' => 'Technicien eau & assainissement', 'label_en' => 'Water & sanitation technician', 'keywords' => 'eau usée'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'finance',
        'name_fr' => 'Banque, finance & assurance',
        'name_en' => 'Banking, finance & insurance',
        'professions' => [
            [
                'slug' => 'accountant',
                'name_fr' => 'Comptable',
                'name_en' => 'Accountant',
                'suggestions' => [
                    ['label_fr' => 'Comptable général', 'label_en' => 'General accountant', 'keywords' => 'comptabilite bilan'],
                    ['label_fr' => 'Expert-comptable', 'label_en' => 'Chartered accountant', 'keywords' => 'audit expertise'],
                    ['label_fr' => 'Gestionnaire de paie', 'label_en' => 'Payroll manager', 'keywords' => 'paie rh social'],
                ],
            ],
            [
                'slug' => 'financial-controller',
                'name_fr' => 'Contrôle de gestion',
                'name_en' => 'Management control',
                'suggestions' => [
                    ['label_fr' => 'Contrôleur de gestion', 'label_en' => 'Financial controller', 'keywords' => 'reporting kpi'],
                    ['label_fr' => 'Analyste FP&A', 'label_en' => 'FP&A analyst', 'keywords' => 'budget previsionnel'],
                ],
            ],
            [
                'slug' => 'cfo',
                'name_fr' => 'Direction financière',
                'name_en' => 'Finance leadership',
                'suggestions' => [
                    ['label_fr' => 'Directeur administratif et financier', 'label_en' => 'Chief financial officer', 'keywords' => 'daf finance'],
                    ['label_fr' => 'Trésorier', 'label_en' => 'Treasurer', 'keywords' => 'tresorerie cash'],
                ],
            ],
            [
                'slug' => 'bank-advisor',
                'name_fr' => 'Banque & assurance',
                'name_en' => 'Banking & insurance',
                'suggestions' => [
                    ['label_fr' => 'Conseiller clientèle bancaire', 'label_en' => 'Bank relationship manager', 'keywords' => 'banque credit'],
                    ['label_fr' => 'Gestionnaire sinistres', 'label_en' => 'Claims manager', 'keywords' => 'assurance sinistre'],
                    ['label_fr' => 'Analyste risque crédit', 'label_en' => 'Credit risk analyst', 'keywords' => 'risque scoring'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'commerce',
        'name_fr' => 'Commerce & distribution',
        'name_en' => 'Retail & distribution',
        'professions' => [
            [
                'slug' => 'sales-rep',
                'name_fr' => 'Commercial',
                'name_en' => 'Sales representative',
                'suggestions' => [
                    ['label_fr' => 'Commercial B2B', 'label_en' => 'B2B sales', 'keywords' => 'prospection grand compte'],
                    ['label_fr' => 'Key Account Manager', 'label_en' => 'Key Account Manager', 'keywords' => 'compte cle'],
                    ['label_fr' => 'Business developer', 'label_en' => 'Business developer', 'keywords' => 'bizdev partenariats'],
                ],
            ],
            [
                'slug' => 'retail-manager',
                'name_fr' => 'Retail & magasin',
                'name_en' => 'Store & retail',
                'suggestions' => [
                    ['label_fr' => 'Responsable de magasin', 'label_en' => 'Store manager', 'keywords' => 'retail management'],
                    ['label_fr' => 'Chef de rayon', 'label_en' => 'Department manager', 'keywords' => 'grande distribution'],
                ],
            ],
            [
                'slug' => 'ecommerce',
                'name_fr' => 'E-commerce',
                'name_en' => 'E-commerce',
                'suggestions' => [
                    ['label_fr' => 'Responsable e-commerce', 'label_en' => 'E-commerce manager', 'keywords' => 'marketplace shopify'],
                    ['label_fr' => 'Category manager', 'label_en' => 'Category manager', 'keywords' => 'assortiment merchandising'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'hospitality',
        'name_fr' => 'Hôtellerie, tourisme & restauration',
        'name_en' => 'Hospitality, tourism & catering',
        'professions' => [
            [
                'slug' => 'chef',
                'name_fr' => 'Cuisine',
                'name_en' => 'Culinary',
                'suggestions' => [
                    ['label_fr' => 'Chef de cuisine', 'label_en' => 'Head chef', 'keywords' => 'cuisine restaurant'],
                    ['label_fr' => 'Chef de partie', 'label_en' => 'Station chef', 'keywords' => 'brigade'],
                    ['label_fr' => 'Pâtissier(ère)', 'label_en' => 'Pastry chef', 'keywords' => 'patisserie'],
                ],
            ],
            [
                'slug' => 'hotel-staff',
                'name_fr' => 'Hôtellerie & accueil',
                'name_en' => 'Hotel & guest services',
                'suggestions' => [
                    ['label_fr' => 'Réceptionniste hôtel', 'label_en' => 'Hotel receptionist', 'keywords' => 'accueil front office'],
                    ['label_fr' => 'Revenue manager', 'label_en' => 'Revenue manager', 'keywords' => 'yield pricing'],
                    ['label_fr' => 'Gouvernant(e) d\'étages', 'label_en' => 'Housekeeping manager', 'keywords' => 'housekeeping'],
                ],
            ],
            [
                'slug' => 'tourism',
                'name_fr' => 'Tourisme',
                'name_en' => 'Tourism',
                'suggestions' => [
                    ['label_fr' => 'Conseiller voyages', 'label_en' => 'Travel advisor', 'keywords' => 'agence tourisme'],
                    ['label_fr' => 'Guide touristique', 'label_en' => 'Tour guide', 'keywords' => 'visite circuit'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'transport-logistics',
        'name_fr' => 'Transport & logistique',
        'name_en' => 'Transport & logistics',
        'professions' => [
            [
                'slug' => 'logistics-manager',
                'name_fr' => 'Logistique',
                'name_en' => 'Logistics',
                'suggestions' => [
                    ['label_fr' => 'Responsable logistique', 'label_en' => 'Logistics manager', 'keywords' => 'supply chain entrepot'],
                    ['label_fr' => 'Planificateur transport', 'label_en' => 'Transport planner', 'keywords' => 'fleet TMS'],
                    ['label_fr' => 'Gestionnaire stocks', 'label_en' => 'Inventory controller', 'keywords' => 'wms stock'],
                ],
            ],
            [
                'slug' => 'supply-chain',
                'name_fr' => 'Supply chain',
                'name_en' => 'Supply chain',
                'suggestions' => [
                    ['label_fr' => 'Supply chain manager', 'label_en' => 'Supply chain manager', 'keywords' => 'approvisionnements S&OP'],
                    ['label_fr' => 'Acheteur', 'label_en' => 'Buyer / procurement', 'keywords' => 'achats negociation'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'consulting',
        'name_fr' => 'Conseil & services aux entreprises',
        'name_en' => 'Consulting & professional services',
        'professions' => [
            [
                'slug' => 'management-consultant',
                'name_fr' => 'Consultant',
                'name_en' => 'Consultant',
                'suggestions' => [
                    ['label_fr' => 'Consultant stratégie', 'label_en' => 'Strategy consultant', 'keywords' => 'conseil transformation'],
                    ['label_fr' => 'Consultant SI', 'label_en' => 'IT consultant', 'keywords' => 'erp integration'],
                    ['label_fr' => 'Consultant RH', 'label_en' => 'HR consultant', 'keywords' => 'organisation talent'],
                ],
            ],
            [
                'slug' => 'legal',
                'name_fr' => 'Juridique',
                'name_en' => 'Legal',
                'suggestions' => [
                    ['label_fr' => 'Juriste d\'entreprise', 'label_en' => 'In-house counsel', 'keywords' => 'contrats droit affaires'],
                    ['label_fr' => 'Avocat', 'label_en' => 'Lawyer', 'keywords' => 'contentieux conseil'],
                ],
            ],
            [
                'slug' => 'hr',
                'name_fr' => 'Ressources humaines',
                'name_en' => 'Human resources',
                'suggestions' => [
                    ['label_fr' => 'Chargé(e) de recrutement', 'label_en' => 'Recruiter', 'keywords' => 'sourcing talent acquisition'],
                    ['label_fr' => 'Responsable RH', 'label_en' => 'HR manager', 'keywords' => 'rh administration'],
                    ['label_fr' => 'Responsable formation', 'label_en' => 'L&D manager', 'keywords' => 'formation developpement'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'marketing-comms',
        'name_fr' => 'Marketing, communication & médias',
        'name_en' => 'Marketing, communications & media',
        'professions' => [
            [
                'slug' => 'digital-marketer',
                'name_fr' => 'Marketing digital',
                'name_en' => 'Digital marketing',
                'suggestions' => [
                    ['label_fr' => 'Growth marketer', 'label_en' => 'Growth marketer', 'keywords' => 'acquisition growth'],
                    ['label_fr' => 'SEO / SEA', 'label_en' => 'SEO / SEA specialist', 'keywords' => 'google ads referencement'],
                    ['label_fr' => 'Social media manager', 'label_en' => 'Social media manager', 'keywords' => 'community networks'],
                ],
            ],
            [
                'slug' => 'brand-comms',
                'name_fr' => 'Communication & marque',
                'name_en' => 'Brand & communications',
                'suggestions' => [
                    ['label_fr' => 'Chargé(e) de communication', 'label_en' => 'Communications officer', 'keywords' => 'rp presse interne'],
                    ['label_fr' => 'Brand manager', 'label_en' => 'Brand manager', 'keywords' => 'marque positionnement'],
                ],
            ],
            [
                'slug' => 'content-creator',
                'name_fr' => 'Contenu & création',
                'name_en' => 'Content & creative',
                'suggestions' => [
                    ['label_fr' => 'Rédacteur(trice) web', 'label_en' => 'Web copywriter', 'keywords' => 'contenu editorial'],
                    ['label_fr' => 'Motion designer', 'label_en' => 'Motion designer', 'keywords' => 'video after effects'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'education',
        'name_fr' => 'Éducation & formation',
        'name_en' => 'Education & training',
        'professions' => [
            [
                'slug' => 'teacher',
                'name_fr' => 'Enseignant(e)',
                'name_en' => 'Teacher',
                'suggestions' => [
                    ['label_fr' => 'Professeur des écoles', 'label_en' => 'Primary school teacher', 'keywords' => 'primaire pedagogie'],
                    ['label_fr' => 'Enseignant secondaire', 'label_en' => 'Secondary teacher', 'keywords' => 'college lycee'],
                    ['label_fr' => 'Formateur professionnel', 'label_en' => 'Corporate trainer', 'keywords' => 'formation adultes'],
                ],
            ],
            [
                'slug' => 'edtech',
                'name_fr' => 'Ingénierie pédagogique',
                'name_en' => 'Learning design',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur pédagogique', 'label_en' => 'Instructional designer', 'keywords' => 'elearning scorm'],
                    ['label_fr' => 'Responsable centre de formation', 'label_en' => 'Training center manager', 'keywords' => 'cfa organisme'],
                ],
            ],
        ],
    ],
];
