<?php

/**
 * Taxonomie Bibliothèque — discipline / sous-discipline / sous-sous-discipline.
 */
return [
    [
        'slug' => 'informatique',
        'name_fr' => 'Informatique',
        'name_en' => 'Computer science & IT',
        'children' => [
            [
                'slug' => 'developpement',
                'name_fr' => 'Développement informatique',
                'name_en' => 'Software development',
                'children' => [
                    ['slug' => 'programmation-systeme', 'name_fr' => 'Programmation système', 'name_en' => 'Systems programming'],
                    ['slug' => 'developpement-web', 'name_fr' => 'Développement web', 'name_en' => 'Web development'],
                    ['slug' => 'developpement-mobile', 'name_fr' => 'Développement mobile', 'name_en' => 'Mobile development'],
                    ['slug' => 'backend-api', 'name_fr' => 'Backend & API', 'name_en' => 'Backend & APIs'],
                ],
            ],
            [
                'slug' => 'data-ia',
                'name_fr' => 'Data & intelligence artificielle',
                'name_en' => 'Data & AI',
                'children' => [
                    ['slug' => 'data-engineering', 'name_fr' => 'Data engineering', 'name_en' => 'Data engineering'],
                    ['slug' => 'data-science-ml', 'name_fr' => 'Data science & machine learning', 'name_en' => 'Data science & ML'],
                    ['slug' => 'bi-analytics', 'name_fr' => 'Business intelligence', 'name_en' => 'Business intelligence'],
                ],
            ],
            [
                'slug' => 'infra-cyber',
                'name_fr' => 'Infrastructure & cybersécurité',
                'name_en' => 'Infrastructure & cybersecurity',
                'children' => [
                    ['slug' => 'devops-cloud', 'name_fr' => 'DevOps & cloud', 'name_en' => 'DevOps & cloud'],
                    ['slug' => 'reseaux-systemes', 'name_fr' => 'Réseaux & systèmes', 'name_en' => 'Networks & systems'],
                    ['slug' => 'cybersecurite', 'name_fr' => 'Cybersécurité', 'name_en' => 'Cybersecurity'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'ingenierie',
        'name_fr' => 'Ingénierie',
        'name_en' => 'Engineering',
        'children' => [
            [
                'slug' => 'genie-mecanique',
                'name_fr' => 'Génie mécanique',
                'name_en' => 'Mechanical engineering',
                'children' => [
                    ['slug' => 'conception-cao', 'name_fr' => 'Conception & CAO', 'name_en' => 'Design & CAD'],
                    ['slug' => 'thermodynamique', 'name_fr' => 'Thermodynamique & fluides', 'name_en' => 'Thermodynamics & fluids'],
                    ['slug' => 'maintenance-industrielle', 'name_fr' => 'Maintenance industrielle', 'name_en' => 'Industrial maintenance'],
                ],
            ],
            [
                'slug' => 'genie-electrique',
                'name_fr' => 'Génie électrique',
                'name_en' => 'Electrical engineering',
                'children' => [
                    ['slug' => 'electrotechnique', 'name_fr' => 'Électrotechnique', 'name_en' => 'Electrical power'],
                    ['slug' => 'electronique', 'name_fr' => 'Électronique', 'name_en' => 'Electronics'],
                    ['slug' => 'automatismes', 'name_fr' => 'Automatismes & contrôle', 'name_en' => 'Automation & control'],
                ],
            ],
            [
                'slug' => 'genie-civil',
                'name_fr' => 'Génie civil',
                'name_en' => 'Civil engineering',
                'children' => [
                    ['slug' => 'structures', 'name_fr' => 'Structures', 'name_en' => 'Structures'],
                    ['slug' => 'geotechnique', 'name_fr' => 'Géotechnique', 'name_en' => 'Geotechnics'],
                    ['slug' => 'hydraulique-urbain', 'name_fr' => 'Hydraulique & urbain', 'name_en' => 'Hydraulics & urban'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'sante',
        'name_fr' => 'Santé & sciences médicales',
        'name_en' => 'Health & medical sciences',
        'children' => [
            [
                'slug' => 'medecine',
                'name_fr' => 'Médecine',
                'name_en' => 'Medicine',
                'children' => [
                    ['slug' => 'medecine-generale', 'name_fr' => 'Médecine générale', 'name_en' => 'General practice'],
                    ['slug' => 'specialites-cliniques', 'name_fr' => 'Spécialités cliniques', 'name_en' => 'Clinical specialties'],
                    ['slug' => 'sante-publique', 'name_fr' => 'Santé publique', 'name_en' => 'Public health'],
                ],
            ],
            [
                'slug' => 'paramedical',
                'name_fr' => 'Paramédical',
                'name_en' => 'Allied health',
                'children' => [
                    ['slug' => 'soins-infirmiers', 'name_fr' => 'Soins infirmiers', 'name_en' => 'Nursing'],
                    ['slug' => 'reeducation', 'name_fr' => 'Rééducation', 'name_en' => 'Rehabilitation'],
                    ['slug' => 'imagerie-biologie', 'name_fr' => 'Imagerie & biologie', 'name_en' => 'Imaging & lab'],
                ],
            ],
            [
                'slug' => 'pharma-biotech',
                'name_fr' => 'Pharmacie & biotech',
                'name_en' => 'Pharmacy & biotech',
                'children' => [
                    ['slug' => 'pharmacologie', 'name_fr' => 'Pharmacologie', 'name_en' => 'Pharmacology'],
                    ['slug' => 'industrie-pharma', 'name_fr' => 'Industrie pharmaceutique', 'name_en' => 'Pharma industry'],
                    ['slug' => 'recherche-clinique', 'name_fr' => 'Recherche clinique', 'name_en' => 'Clinical research'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'droit',
        'name_fr' => 'Droit',
        'name_en' => 'Law',
        'children' => [
            [
                'slug' => 'droit-prive',
                'name_fr' => 'Droit privé',
                'name_en' => 'Private law',
                'children' => [
                    ['slug' => 'droit-affaires', 'name_fr' => 'Droit des affaires', 'name_en' => 'Business law'],
                    ['slug' => 'droit-travail', 'name_fr' => 'Droit du travail', 'name_en' => 'Labour law'],
                    ['slug' => 'droit-contrats', 'name_fr' => 'Contrats & obligations', 'name_en' => 'Contracts & obligations'],
                ],
            ],
            [
                'slug' => 'droit-public',
                'name_fr' => 'Droit public',
                'name_en' => 'Public law',
                'children' => [
                    ['slug' => 'droit-administratif', 'name_fr' => 'Droit administratif', 'name_en' => 'Administrative law'],
                    ['slug' => 'droit-constitutionnel', 'name_fr' => 'Droit constitutionnel', 'name_en' => 'Constitutional law'],
                    ['slug' => 'droit-fiscal', 'name_fr' => 'Droit fiscal', 'name_en' => 'Tax law'],
                ],
            ],
            [
                'slug' => 'droit-international',
                'name_fr' => 'Droit international',
                'name_en' => 'International law',
                'children' => [
                    ['slug' => 'droit-commerce-intl', 'name_fr' => 'Commerce international', 'name_en' => 'International trade law'],
                    ['slug' => 'compliance-reglementaire', 'name_fr' => 'Compliance réglementaire', 'name_en' => 'Regulatory compliance'],
                    ['slug' => 'propriete-intellectuelle', 'name_fr' => 'Propriété intellectuelle', 'name_en' => 'Intellectual property'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'finance',
        'name_fr' => 'Banque, finance & assurance',
        'name_en' => 'Banking, finance & insurance',
        'children' => [
            [
                'slug' => 'finance-entreprise',
                'name_fr' => 'Finance d\'entreprise',
                'name_en' => 'Corporate finance',
                'children' => [
                    ['slug' => 'comptabilite', 'name_fr' => 'Comptabilité', 'name_en' => 'Accounting'],
                    ['slug' => 'controle-gestion', 'name_fr' => 'Contrôle de gestion', 'name_en' => 'Management control'],
                    ['slug' => 'tresorerie', 'name_fr' => 'Trésorerie', 'name_en' => 'Treasury'],
                ],
            ],
            [
                'slug' => 'marches-financiers',
                'name_fr' => 'Marchés financiers',
                'name_en' => 'Financial markets',
                'children' => [
                    ['slug' => 'analyse-financiere', 'name_fr' => 'Analyse financière', 'name_en' => 'Financial analysis'],
                    ['slug' => 'investissement', 'name_fr' => 'Investissement & asset management', 'name_en' => 'Investment & asset management'],
                    ['slug' => 'gestion-risques', 'name_fr' => 'Gestion des risques', 'name_en' => 'Risk management'],
                ],
            ],
            [
                'slug' => 'assurance',
                'name_fr' => 'Assurance',
                'name_en' => 'Insurance',
                'children' => [
                    ['slug' => 'actuariat', 'name_fr' => 'Actuariat', 'name_en' => 'Actuarial science'],
                    ['slug' => 'souscription-sinistres', 'name_fr' => 'Souscription & sinistres', 'name_en' => 'Underwriting & claims'],
                    ['slug' => 'produits-assurance', 'name_fr' => 'Produits d\'assurance', 'name_en' => 'Insurance products'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'management',
        'name_fr' => 'Management & stratégie',
        'name_en' => 'Management & strategy',
        'children' => [
            [
                'slug' => 'strategie-entreprise',
                'name_fr' => 'Stratégie d\'entreprise',
                'name_en' => 'Business strategy',
                'children' => [
                    ['slug' => 'strategie-corporate', 'name_fr' => 'Stratégie corporate', 'name_en' => 'Corporate strategy'],
                    ['slug' => 'innovation', 'name_fr' => 'Innovation & transformation', 'name_en' => 'Innovation & transformation'],
                    ['slug' => 'entrepreneuriat', 'name_fr' => 'Entrepreneuriat', 'name_en' => 'Entrepreneurship'],
                ],
            ],
            [
                'slug' => 'operations',
                'name_fr' => 'Opérations & excellence opérationnelle',
                'name_en' => 'Operations excellence',
                'children' => [
                    ['slug' => 'lean-six-sigma', 'name_fr' => 'Lean & Six Sigma', 'name_en' => 'Lean & Six Sigma'],
                    ['slug' => 'gestion-projet', 'name_fr' => 'Gestion de projet', 'name_en' => 'Project management'],
                    ['slug' => 'qualite', 'name_fr' => 'Qualité & processus', 'name_en' => 'Quality & processes'],
                ],
            ],
            [
                'slug' => 'leadership',
                'name_fr' => 'Leadership & organisation',
                'name_en' => 'Leadership & organization',
                'children' => [
                    ['slug' => 'management-equipes', 'name_fr' => 'Management d\'équipes', 'name_en' => 'Team management'],
                    ['slug' => 'changement', 'name_fr' => 'Conduite du changement', 'name_en' => 'Change management'],
                    ['slug' => 'gouvernance', 'name_fr' => 'Gouvernance', 'name_en' => 'Governance'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'marketing-comms',
        'name_fr' => 'Marketing & communication',
        'name_en' => 'Marketing & communications',
        'children' => [
            [
                'slug' => 'marketing-strategie',
                'name_fr' => 'Marketing stratégique',
                'name_en' => 'Strategic marketing',
                'children' => [
                    ['slug' => 'brand-management', 'name_fr' => 'Brand management', 'name_en' => 'Brand management'],
                    ['slug' => 'etudes-marche', 'name_fr' => 'Études de marché', 'name_en' => 'Market research'],
                    ['slug' => 'pricing', 'name_fr' => 'Pricing & offre', 'name_en' => 'Pricing & offer'],
                ],
            ],
            [
                'slug' => 'marketing-digital',
                'name_fr' => 'Marketing digital',
                'name_en' => 'Digital marketing',
                'children' => [
                    ['slug' => 'seo-sea', 'name_fr' => 'SEO & SEA', 'name_en' => 'SEO & SEA'],
                    ['slug' => 'social-media', 'name_fr' => 'Social media & community', 'name_en' => 'Social media & community'],
                    ['slug' => 'crm-automation', 'name_fr' => 'CRM & automation', 'name_en' => 'CRM & automation'],
                ],
            ],
            [
                'slug' => 'communication',
                'name_fr' => 'Communication',
                'name_en' => 'Communications',
                'children' => [
                    ['slug' => 'relations-presse', 'name_fr' => 'Relations presse & RP', 'name_en' => 'PR & press relations'],
                    ['slug' => 'communication-corporate', 'name_fr' => 'Communication corporate', 'name_en' => 'Corporate communications'],
                    ['slug' => 'contenu-creation', 'name_fr' => 'Contenu & création', 'name_en' => 'Content & creative'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'rh',
        'name_fr' => 'Ressources humaines',
        'name_en' => 'Human resources',
        'children' => [
            [
                'slug' => 'talent',
                'name_fr' => 'Talent & recrutement',
                'name_en' => 'Talent & recruitment',
                'children' => [
                    ['slug' => 'recrutement', 'name_fr' => 'Recrutement', 'name_en' => 'Recruitment'],
                    ['slug' => 'marque-employeur', 'name_fr' => 'Marque employeur', 'name_en' => 'Employer branding'],
                    ['slug' => 'onboarding', 'name_fr' => 'Onboarding & intégration', 'name_en' => 'Onboarding'],
                ],
            ],
            [
                'slug' => 'developpement-rh',
                'name_fr' => 'Développement RH',
                'name_en' => 'People development',
                'children' => [
                    ['slug' => 'formation-ld', 'name_fr' => 'Formation & L&D', 'name_en' => 'Learning & development'],
                    ['slug' => 'gestion-carrieres', 'name_fr' => 'Gestion des carrières', 'name_en' => 'Career management'],
                    ['slug' => 'performance', 'name_fr' => 'Performance & évaluation', 'name_en' => 'Performance management'],
                ],
            ],
            [
                'slug' => 'admin-rh',
                'name_fr' => 'Administration RH',
                'name_en' => 'HR administration',
                'children' => [
                    ['slug' => 'paie-social', 'name_fr' => 'Paie & social', 'name_en' => 'Payroll & social'],
                    ['slug' => 'relations-sociales', 'name_fr' => 'Relations sociales', 'name_en' => 'Employee relations'],
                    ['slug' => 'sirh', 'name_fr' => 'SIRH & digital RH', 'name_en' => 'HRIS & HR digital'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'education',
        'name_fr' => 'Éducation & pédagogie',
        'name_en' => 'Education & pedagogy',
        'children' => [
            [
                'slug' => 'enseignement',
                'name_fr' => 'Enseignement',
                'name_en' => 'Teaching',
                'children' => [
                    ['slug' => 'pedagogie', 'name_fr' => 'Pédagogie', 'name_en' => 'Pedagogy'],
                    ['slug' => 'didactique', 'name_fr' => 'Didactique des disciplines', 'name_en' => 'Subject didactics'],
                    ['slug' => 'inclusion', 'name_fr' => 'Inclusion & besoins spécifiques', 'name_en' => 'Inclusion & SEN'],
                ],
            ],
            [
                'slug' => 'formation-pro',
                'name_fr' => 'Formation professionnelle',
                'name_en' => 'Vocational training',
                'children' => [
                    ['slug' => 'ingenierie-pedagogique', 'name_fr' => 'Ingénierie pédagogique', 'name_en' => 'Instructional design'],
                    ['slug' => 'elearning', 'name_fr' => 'E-learning & digital learning', 'name_en' => 'E-learning'],
                    ['slug' => 'evaluation-certification', 'name_fr' => 'Évaluation & certification', 'name_en' => 'Assessment & certification'],
                ],
            ],
            [
                'slug' => 'orientation',
                'name_fr' => 'Orientation & accompagnement',
                'name_en' => 'Guidance & counseling',
                'children' => [
                    ['slug' => 'orientation-scolaire', 'name_fr' => 'Orientation scolaire', 'name_en' => 'Academic guidance'],
                    ['slug' => 'conseil-carriere', 'name_fr' => 'Conseil en carrière', 'name_en' => 'Career counseling'],
                    ['slug' => 'vae', 'name_fr' => 'VAE & validation des acquis', 'name_en' => 'Prior learning recognition'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'energie',
        'name_fr' => 'Énergie & environnement',
        'name_en' => 'Energy & environment',
        'children' => [
            [
                'slug' => 'energies',
                'name_fr' => 'Énergies',
                'name_en' => 'Energy',
                'children' => [
                    ['slug' => 'renouvelables', 'name_fr' => 'Énergies renouvelables', 'name_en' => 'Renewables'],
                    ['slug' => 'efficacite-energetique', 'name_fr' => 'Efficacité énergétique', 'name_en' => 'Energy efficiency'],
                    ['slug' => 'reseaux-electriques', 'name_fr' => 'Réseaux électriques', 'name_en' => 'Power grids'],
                ],
            ],
            [
                'slug' => 'environnement',
                'name_fr' => 'Environnement',
                'name_en' => 'Environment',
                'children' => [
                    ['slug' => 'gestion-dechets', 'name_fr' => 'Gestion des déchets', 'name_en' => 'Waste management'],
                    ['slug' => 'eau-assainissement', 'name_fr' => 'Eau & assainissement', 'name_en' => 'Water & sanitation'],
                    ['slug' => 'climat-esg', 'name_fr' => 'Climat & ESG', 'name_en' => 'Climate & ESG'],
                ],
            ],
            [
                'slug' => 'oil-gas',
                'name_fr' => 'Pétrole & gaz',
                'name_en' => 'Oil & gas',
                'children' => [
                    ['slug' => 'upstream', 'name_fr' => 'Upstream / exploration', 'name_en' => 'Upstream / exploration'],
                    ['slug' => 'midstream-downstream', 'name_fr' => 'Midstream & downstream', 'name_en' => 'Midstream & downstream'],
                    ['slug' => 'hse-energie', 'name_fr' => 'HSE énergie', 'name_en' => 'Energy HSE'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'btp',
        'name_fr' => 'BTP & construction',
        'name_en' => 'Construction & built environment',
        'children' => [
            [
                'slug' => 'construction',
                'name_fr' => 'Construction',
                'name_en' => 'Construction',
                'children' => [
                    ['slug' => 'conduite-travaux', 'name_fr' => 'Conduite de travaux', 'name_en' => 'Site management'],
                    ['slug' => 'methodes-planning', 'name_fr' => 'Méthodes & planning', 'name_en' => 'Methods & planning'],
                    ['slug' => 'economie-construction', 'name_fr' => 'Économie de la construction', 'name_en' => 'Quantity surveying'],
                ],
            ],
            [
                'slug' => 'architecture-urbanisme',
                'name_fr' => 'Architecture & urbanisme',
                'name_en' => 'Architecture & urban planning',
                'children' => [
                    ['slug' => 'architecture', 'name_fr' => 'Architecture', 'name_en' => 'Architecture'],
                    ['slug' => 'urbanisme', 'name_fr' => 'Urbanisme', 'name_en' => 'Urban planning'],
                    ['slug' => 'bim', 'name_fr' => 'BIM & maquette numérique', 'name_en' => 'BIM'],
                ],
            ],
            [
                'slug' => 'second-oeuvre',
                'name_fr' => 'Second œuvre & techniques',
                'name_en' => 'Finishing & building services',
                'children' => [
                    ['slug' => 'cvc-fluides', 'name_fr' => 'CVC & fluides', 'name_en' => 'HVAC & fluids'],
                    ['slug' => 'electricite-batiment', 'name_fr' => 'Électricité bâtiment', 'name_en' => 'Building electrics'],
                    ['slug' => 'qse-chantier', 'name_fr' => 'QSE chantier', 'name_en' => 'Site QHSE'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'industrie',
        'name_fr' => 'Industrie & production',
        'name_en' => 'Industry & manufacturing',
        'children' => [
            [
                'slug' => 'production',
                'name_fr' => 'Production',
                'name_en' => 'Production',
                'children' => [
                    ['slug' => 'pilotage-production', 'name_fr' => 'Pilotage de production', 'name_en' => 'Production management'],
                    ['slug' => 'methodes-industrialisation', 'name_fr' => 'Méthodes & industrialisation', 'name_en' => 'Methods & industrialization'],
                    ['slug' => 'maintenance-fiabilite', 'name_fr' => 'Maintenance & fiabilité', 'name_en' => 'Maintenance & reliability'],
                ],
            ],
            [
                'slug' => 'supply-industrie',
                'name_fr' => 'Supply & achats industriels',
                'name_en' => 'Industrial supply & procurement',
                'children' => [
                    ['slug' => 'supply-chain', 'name_fr' => 'Supply chain', 'name_en' => 'Supply chain'],
                    ['slug' => 'achats-industriels', 'name_fr' => 'Achats industriels', 'name_en' => 'Industrial procurement'],
                    ['slug' => 'logistique-usine', 'name_fr' => 'Logistique d\'usine', 'name_en' => 'Plant logistics'],
                ],
            ],
            [
                'slug' => 'qhse-industrie',
                'name_fr' => 'QHSE industriel',
                'name_en' => 'Industrial QHSE',
                'children' => [
                    ['slug' => 'securite-industrielle', 'name_fr' => 'Sécurité industrielle', 'name_en' => 'Industrial safety'],
                    ['slug' => 'environnement-usine', 'name_fr' => 'Environnement usine', 'name_en' => 'Plant environment'],
                    ['slug' => 'qualite-industrielle', 'name_fr' => 'Qualité industrielle', 'name_en' => 'Industrial quality'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'transport',
        'name_fr' => 'Transport & logistique',
        'name_en' => 'Transport & logistics',
        'children' => [
            [
                'slug' => 'logistique',
                'name_fr' => 'Logistique',
                'name_en' => 'Logistics',
                'children' => [
                    ['slug' => 'entrepot-wms', 'name_fr' => 'Entrepôt & WMS', 'name_en' => 'Warehouse & WMS'],
                    ['slug' => 'transport-routier', 'name_fr' => 'Transport routier', 'name_en' => 'Road transport'],
                    ['slug' => 'last-mile', 'name_fr' => 'Last mile & messagerie', 'name_en' => 'Last mile & courier'],
                ],
            ],
            [
                'slug' => 'fret-international',
                'name_fr' => 'Fret international',
                'name_en' => 'International freight',
                'children' => [
                    ['slug' => 'transit-douane', 'name_fr' => 'Transit & douane', 'name_en' => 'Forwarding & customs'],
                    ['slug' => 'fret-maritime-aerien', 'name_fr' => 'Fret maritime & aérien', 'name_en' => 'Sea & air freight'],
                    ['slug' => 'multimodal', 'name_fr' => 'Multimodal', 'name_en' => 'Multimodal'],
                ],
            ],
            [
                'slug' => 'mobilite',
                'name_fr' => 'Mobilité & voyageurs',
                'name_en' => 'Mobility & passengers',
                'children' => [
                    ['slug' => 'transport-urbain', 'name_fr' => 'Transport urbain', 'name_en' => 'Urban transport'],
                    ['slug' => 'ferroviaire', 'name_fr' => 'Ferroviaire', 'name_en' => 'Rail'],
                    ['slug' => 'aviation', 'name_fr' => 'Aviation', 'name_en' => 'Aviation'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'commerce',
        'name_fr' => 'Commerce & distribution',
        'name_en' => 'Retail & distribution',
        'children' => [
            [
                'slug' => 'retail',
                'name_fr' => 'Retail & magasin',
                'name_en' => 'Retail & store',
                'children' => [
                    ['slug' => 'management-magasin', 'name_fr' => 'Management de magasin', 'name_en' => 'Store management'],
                    ['slug' => 'merchandising', 'name_fr' => 'Merchandising', 'name_en' => 'Merchandising'],
                    ['slug' => 'vente-conseil', 'name_fr' => 'Vente & conseil client', 'name_en' => 'Sales & customer advice'],
                ],
            ],
            [
                'slug' => 'vente-b2b',
                'name_fr' => 'Vente B2B',
                'name_en' => 'B2B sales',
                'children' => [
                    ['slug' => 'techniques-vente', 'name_fr' => 'Techniques de vente', 'name_en' => 'Sales techniques'],
                    ['slug' => 'grands-comptes', 'name_fr' => 'Grands comptes / KAM', 'name_en' => 'Key accounts'],
                    ['slug' => 'negociation', 'name_fr' => 'Négociation commerciale', 'name_en' => 'Commercial negotiation'],
                ],
            ],
            [
                'slug' => 'ecommerce',
                'name_fr' => 'E-commerce',
                'name_en' => 'E-commerce',
                'children' => [
                    ['slug' => 'ops-ecommerce', 'name_fr' => 'Opérations e-commerce', 'name_en' => 'E-commerce operations'],
                    ['slug' => 'marketplace', 'name_fr' => 'Marketplace', 'name_en' => 'Marketplace'],
                    ['slug' => 'ux-conversion', 'name_fr' => 'UX & conversion', 'name_en' => 'UX & conversion'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'hotellerie',
        'name_fr' => 'Hôtellerie, tourisme & restauration',
        'name_en' => 'Hospitality, tourism & catering',
        'children' => [
            [
                'slug' => 'hotellerie',
                'name_fr' => 'Hôtellerie',
                'name_en' => 'Hospitality',
                'children' => [
                    ['slug' => 'front-office', 'name_fr' => 'Front office & accueil', 'name_en' => 'Front office'],
                    ['slug' => 'housekeeping', 'name_fr' => 'Housekeeping', 'name_en' => 'Housekeeping'],
                    ['slug' => 'revenue-management', 'name_fr' => 'Revenue management', 'name_en' => 'Revenue management'],
                ],
            ],
            [
                'slug' => 'restauration',
                'name_fr' => 'Restauration',
                'name_en' => 'Catering',
                'children' => [
                    ['slug' => 'cuisine', 'name_fr' => 'Cuisine & production', 'name_en' => 'Kitchen & production'],
                    ['slug' => 'service-salle', 'name_fr' => 'Service en salle', 'name_en' => 'Dining service'],
                    ['slug' => 'fnb-management', 'name_fr' => 'Management F&B', 'name_en' => 'F&B management'],
                ],
            ],
            [
                'slug' => 'tourisme',
                'name_fr' => 'Tourisme',
                'name_en' => 'Tourism',
                'children' => [
                    ['slug' => 'agence-voyages', 'name_fr' => 'Agence & voyages', 'name_en' => 'Travel agency'],
                    ['slug' => 'guides-circuits', 'name_fr' => 'Guides & circuits', 'name_en' => 'Guides & tours'],
                    ['slug' => 'destination-management', 'name_fr' => 'Destination management', 'name_en' => 'Destination management'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'langues',
        'name_fr' => 'Langues & communication interculturelle',
        'name_en' => 'Languages & intercultural communication',
        'children' => [
            [
                'slug' => 'langues-vivantes',
                'name_fr' => 'Langues vivantes',
                'name_en' => 'Modern languages',
                'children' => [
                    ['slug' => 'anglais-pro', 'name_fr' => 'Anglais professionnel', 'name_en' => 'Business English'],
                    ['slug' => 'francais-fle', 'name_fr' => 'Français / FLE', 'name_en' => 'French / FLE'],
                    ['slug' => 'autres-langues', 'name_fr' => 'Autres langues', 'name_en' => 'Other languages'],
                ],
            ],
            [
                'slug' => 'traduction',
                'name_fr' => 'Traduction & interprétation',
                'name_en' => 'Translation & interpreting',
                'children' => [
                    ['slug' => 'traduction-technique', 'name_fr' => 'Traduction technique', 'name_en' => 'Technical translation'],
                    ['slug' => 'traduction-juridique', 'name_fr' => 'Traduction juridique', 'name_en' => 'Legal translation'],
                    ['slug' => 'interpretation', 'name_fr' => 'Interprétation', 'name_en' => 'Interpreting'],
                ],
            ],
            [
                'slug' => 'communication-orale',
                'name_fr' => 'Communication orale & écrite',
                'name_en' => 'Oral & written communication',
                'children' => [
                    ['slug' => 'prise-parole', 'name_fr' => 'Prise de parole', 'name_en' => 'Public speaking'],
                    ['slug' => 'redaction-pro', 'name_fr' => 'Rédaction professionnelle', 'name_en' => 'Professional writing'],
                    ['slug' => 'interculturel', 'name_fr' => 'Communication interculturelle', 'name_en' => 'Intercultural communication'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'sciences',
        'name_fr' => 'Sciences & mathématiques',
        'name_en' => 'Sciences & mathematics',
        'children' => [
            [
                'slug' => 'mathematiques',
                'name_fr' => 'Mathématiques',
                'name_en' => 'Mathematics',
                'children' => [
                    ['slug' => 'analyse-algebre', 'name_fr' => 'Analyse & algèbre', 'name_en' => 'Analysis & algebra'],
                    ['slug' => 'statistiques-proba', 'name_fr' => 'Statistiques & probabilités', 'name_en' => 'Statistics & probability'],
                    ['slug' => 'maths-appliquees', 'name_fr' => 'Mathématiques appliquées', 'name_en' => 'Applied mathematics'],
                ],
            ],
            [
                'slug' => 'physique-chimie',
                'name_fr' => 'Physique & chimie',
                'name_en' => 'Physics & chemistry',
                'children' => [
                    ['slug' => 'physique', 'name_fr' => 'Physique', 'name_en' => 'Physics'],
                    ['slug' => 'chimie', 'name_fr' => 'Chimie', 'name_en' => 'Chemistry'],
                    ['slug' => 'sciences-materiaux', 'name_fr' => 'Sciences des matériaux', 'name_en' => 'Materials science'],
                ],
            ],
            [
                'slug' => 'sciences-vie',
                'name_fr' => 'Sciences de la vie',
                'name_en' => 'Life sciences',
                'children' => [
                    ['slug' => 'biologie', 'name_fr' => 'Biologie', 'name_en' => 'Biology'],
                    ['slug' => 'biotechnologies', 'name_fr' => 'Biotechnologies', 'name_en' => 'Biotechnology'],
                    ['slug' => 'agronomie', 'name_fr' => 'Agronomie', 'name_en' => 'Agronomy'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'conseil',
        'name_fr' => 'Conseil & services professionnels',
        'name_en' => 'Consulting & professional services',
        'children' => [
            [
                'slug' => 'conseil-management',
                'name_fr' => 'Conseil en management',
                'name_en' => 'Management consulting',
                'children' => [
                    ['slug' => 'strategie-conseil', 'name_fr' => 'Conseil stratégie', 'name_en' => 'Strategy consulting'],
                    ['slug' => 'conseil-si', 'name_fr' => 'Conseil SI', 'name_en' => 'IT consulting'],
                    ['slug' => 'conseil-rh', 'name_fr' => 'Conseil RH', 'name_en' => 'HR consulting'],
                ],
            ],
            [
                'slug' => 'audit-expertise',
                'name_fr' => 'Audit & expertise',
                'name_en' => 'Audit & advisory',
                'children' => [
                    ['slug' => 'audit-financier', 'name_fr' => 'Audit financier', 'name_en' => 'Financial audit'],
                    ['slug' => 'expertise-comptable', 'name_fr' => 'Expertise comptable', 'name_en' => 'Chartered accountancy'],
                    ['slug' => 'conseil-fiscal', 'name_fr' => 'Conseil fiscal', 'name_en' => 'Tax advisory'],
                ],
            ],
            [
                'slug' => 'juridique-services',
                'name_fr' => 'Services juridiques',
                'name_en' => 'Legal services',
                'children' => [
                    ['slug' => 'avocat-pratique', 'name_fr' => 'Pratique de l\'avocat', 'name_en' => 'Legal practice'],
                    ['slug' => 'compliance-ethique', 'name_fr' => 'Compliance & éthique', 'name_en' => 'Compliance & ethics'],
                    ['slug' => 'mediation', 'name_fr' => 'Médiation & contentieux', 'name_en' => 'Mediation & litigation'],
                ],
            ],
        ],
    ],
];
