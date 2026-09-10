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
                'slug' => 'medical-coordinator-director',
                'name_fr' => 'Médecin Coordonnateur / Directeur Médical',
                'name_en' => 'Medical Coordinator / Medical Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur médical', 'label_en' => 'Medical director', 'keywords' => 'directeur medical direction medicale'],
                    ['label_fr' => 'Médecin coordonnateur', 'label_en' => 'Coordinating physician', 'keywords' => 'coordination parcours medecin'],
                    ['label_fr' => 'Médecine de ville', 'label_en' => 'Primary care medicine', 'keywords' => 'ville generaliste'],
                    ['label_fr' => 'Médecine hospitalière', 'label_en' => 'Hospital medicine', 'keywords' => 'hopital praticien hospitalier'],
                ],
            ],
            [
                'slug' => 'health-facility-director',
                'name_fr' => 'Directeur d\'Établissement de Santé / Médico-Social',
                'name_en' => 'Healthcare / Social Care Facility Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur d\'hôpital', 'label_en' => 'Hospital director', 'keywords' => 'hopital etablissement'],
                    ['label_fr' => 'Directeur EHPAD / médico-social', 'label_en' => 'Nursing home / social care director', 'keywords' => 'ehpad medico-social'],
                    ['label_fr' => 'Direction générale santé', 'label_en' => 'Healthcare executive management', 'keywords' => 'dg management'],
                    ['label_fr' => 'Pilotage budgétaire', 'label_en' => 'Budget & performance management', 'keywords' => 'budget performance'],
                ],
            ],
            [
                'slug' => 'health-nurse-manager',
                'name_fr' => 'Cadre de Santé / Infirmier(ère) Général(e)',
                'name_en' => 'Nurse Manager / Head Nurse',
                'suggestions' => [
                    ['label_fr' => 'Cadre de santé', 'label_en' => 'Nurse manager', 'keywords' => 'cadre encadrement infirmier'],
                    ['label_fr' => 'Infirmier(ère) général(e)', 'label_en' => 'General nurse', 'keywords' => 'ide polyvalent'],
                    ['label_fr' => 'Management de service', 'label_en' => 'Care unit management', 'keywords' => 'service management equipe'],
                    ['label_fr' => 'Qualité des soins', 'label_en' => 'Care quality', 'keywords' => 'qualite soins protocoles'],
                ],
            ],
            [
                'slug' => 'registered-nurse-ide',
                'name_fr' => 'Infirmier(ère) Diplômé(e) d\'État (IDE)',
                'name_en' => 'Registered Nurse (RN)',
                'suggestions' => [
                    ['label_fr' => 'Soins intensifs / réanimation', 'label_en' => 'ICU / critical care', 'keywords' => 'reanimation soins intensifs'],
                    ['label_fr' => 'Bloc opératoire', 'label_en' => 'Operating room', 'keywords' => 'bloc ibode salle operation'],
                    ['label_fr' => 'Pédiatrie', 'label_en' => 'Pediatrics', 'keywords' => 'pediatrie enfants'],
                    ['label_fr' => 'Soins à domicile / HAD', 'label_en' => 'Home care / HAD', 'keywords' => 'had domicile libéral'],
                ],
            ],
            [
                'slug' => 'care-assistant-social',
                'name_fr' => 'Aide-Soignant(e) / Auxiliaire de Vie Sociale',
                'name_en' => 'Care Assistant / Social Care Aide',
                'suggestions' => [
                    ['label_fr' => 'Aide-soignant(e) gériatrie', 'label_en' => 'Geriatric care assistant', 'keywords' => 'ehpad personnes agees'],
                    ['label_fr' => 'Aide à domicile', 'label_en' => 'Home care assistant', 'keywords' => 'domicile had'],
                    ['label_fr' => 'Auxiliaire de vie', 'label_en' => 'Social care aide', 'keywords' => 'avs medico-social'],
                    ['label_fr' => 'Accompagnement du quotidien', 'label_en' => 'Daily living support', 'keywords' => 'hygiene accompagnement'],
                ],
            ],
            [
                'slug' => 'social-worker-cesf',
                'name_fr' => 'Assistant(e) Social(e) / Conseiller(ère) en Économie Sociale et Familiale (CESF)',
                'name_en' => 'Social Worker / Family Counselor (CESF)',
                'suggestions' => [
                    ['label_fr' => 'Assistant(e) social(e)', 'label_en' => 'Social worker', 'keywords' => 'assistant social'],
                    ['label_fr' => 'CESF', 'label_en' => 'Family counselor (CESF)', 'keywords' => 'cesf economie sociale familiale'],
                    ['label_fr' => 'Protection de l\'enfance', 'label_en' => 'Child protection', 'keywords' => 'enfance protection'],
                    ['label_fr' => 'Insertion / accompagnement social', 'label_en' => 'Social inclusion support', 'keywords' => 'insertion accompagnement'],
                ],
            ],
            [
                'slug' => 'clinical-psychologist',
                'name_fr' => 'Psychologue Clinicien(ne)',
                'name_en' => 'Clinical Psychologist',
                'suggestions' => [
                    ['label_fr' => 'Psychologie clinique', 'label_en' => 'Clinical psychology', 'keywords' => 'clinique therapie'],
                    ['label_fr' => 'Psychologie hospitalière', 'label_en' => 'Hospital psychology', 'keywords' => 'hopital psychiatrie'],
                    ['label_fr' => 'Thérapies cognitives et comportementales', 'label_en' => 'CBT', 'keywords' => 'tcc therapie'],
                    ['label_fr' => 'Santé mentale', 'label_en' => 'Mental health', 'keywords' => 'sante mentale'],
                ],
            ],
            [
                'slug' => 'health-quality-risk-manager',
                'name_fr' => 'Responsable de la Qualité et de la Gestion des Risques en Santé',
                'name_en' => 'Healthcare Quality & Risk Manager',
                'suggestions' => [
                    ['label_fr' => 'Qualité en établissement de santé', 'label_en' => 'Healthcare facility quality', 'keywords' => 'qualite etablissement certification'],
                    ['label_fr' => 'Gestion des risques', 'label_en' => 'Risk management', 'keywords' => 'risques patient eigr'],
                    ['label_fr' => 'Accréditation / HAS', 'label_en' => 'Accreditation / HAS', 'keywords' => 'has accreditation'],
                    ['label_fr' => 'Audit & conformité', 'label_en' => 'Audit & compliance', 'keywords' => 'audit conformite'],
                ],
            ],
            [
                'slug' => 'clinical-research-associate',
                'name_fr' => 'Attaché(e) de Recherche Clinique (ARC) / Chef de Projet Recherche Clinique',
                'name_en' => 'Clinical Research Associate / Project Lead',
                'suggestions' => [
                    ['label_fr' => 'ARC terrain', 'label_en' => 'Field CRA', 'keywords' => 'arc monitoring essai clinique'],
                    ['label_fr' => 'Chef de projet recherche clinique', 'label_en' => 'Clinical research project manager', 'keywords' => 'projet essai protocole'],
                    ['label_fr' => 'Essais cliniques phase II-III', 'label_en' => 'Phase II-III trials', 'keywords' => 'phase 2 phase 3'],
                    ['label_fr' => 'GCP / réglementation', 'label_en' => 'GCP / regulatory', 'keywords' => 'gcp ich reglementation'],
                ],
            ],
            [
                'slug' => 'medical-delegate',
                'name_fr' => 'Délégué(e) Médical(e) / Visiteur(se) Médical(e)',
                'name_en' => 'Medical Sales Representative',
                'suggestions' => [
                    ['label_fr' => 'Visite médicale', 'label_en' => 'Medical detailing', 'keywords' => 'vm visite medicale'],
                    ['label_fr' => 'Pharmacie hospitalière', 'label_en' => 'Hospital pharmacy', 'keywords' => 'pharma hopital'],
                    ['label_fr' => 'Spécialistes', 'label_en' => 'Specialist physicians', 'keywords' => 'specialistes medecins'],
                    ['label_fr' => 'Formation médicale continue', 'label_en' => 'Medical education', 'keywords' => 'formation medecins'],
                ],
            ],
            [
                'slug' => 'medtech-pharma-sales-manager',
                'name_fr' => 'Responsable des Ventes - Dispositifs Médicaux et Pharma',
                'name_en' => 'Medical Device & Pharma Sales Manager',
                'suggestions' => [
                    ['label_fr' => 'Dispositifs médicaux', 'label_en' => 'Medical devices', 'keywords' => 'dm dispositif medical'],
                    ['label_fr' => 'Pharma hospitalier', 'label_en' => 'Hospital pharma', 'keywords' => 'pharma hopital'],
                    ['label_fr' => 'Force de vente', 'label_en' => 'Sales force management', 'keywords' => 'equipe commerciale objectifs'],
                    ['label_fr' => 'Négociation grands comptes', 'label_en' => 'Key account negotiation', 'keywords' => 'negociation achats'],
                ],
            ],
            [
                'slug' => 'health-kam',
                'name_fr' => 'Key Account Manager (KAM) - Santé',
                'name_en' => 'Key Account Manager (KAM) - Healthcare',
                'suggestions' => [
                    ['label_fr' => 'Grands comptes hospitaliers', 'label_en' => 'Hospital key accounts', 'keywords' => 'chu hopital kam'],
                    ['label_fr' => 'Renouvellement / upsell', 'label_en' => 'Renewal / upsell', 'keywords' => 'renewal upsell'],
                    ['label_fr' => 'Pharma / medtech', 'label_en' => 'Pharma / medtech', 'keywords' => 'pharma medtech'],
                    ['label_fr' => 'Relation acheteurs santé', 'label_en' => 'Healthcare procurement relations', 'keywords' => 'achats etablissement'],
                ],
            ],
            [
                'slug' => 'health-partnerships-manager',
                'name_fr' => 'Responsable des Partenariats Institutionnels et Réseaux de Soins',
                'name_en' => 'Healthcare Partnerships & Care Networks Manager',
                'suggestions' => [
                    ['label_fr' => 'Partenariats hospitaliers', 'label_en' => 'Hospital partnerships', 'keywords' => 'partenariat etablissement'],
                    ['label_fr' => 'Réseaux de soins', 'label_en' => 'Care networks', 'keywords' => 'reseau de soins parcours'],
                    ['label_fr' => 'Alliances territoriales', 'label_en' => 'Regional alliances', 'keywords' => 'ght territoire'],
                    ['label_fr' => 'Institutions publiques', 'label_en' => 'Public institutions', 'keywords' => 'ars collectivites'],
                ],
            ],
            [
                'slug' => 'pharma-medtech-marketing-manager',
                'name_fr' => 'Responsable Marketing Pharma / Dispositifs Médicaux',
                'name_en' => 'Pharma / Medtech Marketing Manager',
                'suggestions' => [
                    ['label_fr' => 'Marketing pharma', 'label_en' => 'Pharma marketing', 'keywords' => 'pharma marketing produit'],
                    ['label_fr' => 'Marketing dispositifs médicaux', 'label_en' => 'Medtech marketing', 'keywords' => 'medtech dm'],
                    ['label_fr' => 'Lancement produit santé', 'label_en' => 'Healthcare product launch', 'keywords' => 'lancement go to market'],
                    ['label_fr' => 'Conformité promotion', 'label_en' => 'Promotional compliance', 'keywords' => 'reglementation promotion'],
                ],
            ],
            [
                'slug' => 'health-digital-growth',
                'name_fr' => 'Growth Hacker / Responsable Acquisition - Santé Numérique (E-santé)',
                'name_en' => 'Growth / Acquisition Manager - Digital Health',
                'suggestions' => [
                    ['label_fr' => 'E-santé / télésanté', 'label_en' => 'E-health / telehealth', 'keywords' => 'esante telemedecine'],
                    ['label_fr' => 'Acquisition patients / utilisateurs', 'label_en' => 'Patient / user acquisition', 'keywords' => 'acquisition croissance'],
                    ['label_fr' => 'App santé / SaaS', 'label_en' => 'Health app / SaaS', 'keywords' => 'app mobile sante'],
                    ['label_fr' => 'Growth marketing', 'label_en' => 'Growth marketing', 'keywords' => 'growth funnel ab test'],
                ],
            ],
            [
                'slug' => 'health-product-marketing-manager',
                'name_fr' => 'Product Marketing Manager - Solutions de Santé',
                'name_en' => 'Product Marketing Manager - Health Solutions',
                'suggestions' => [
                    ['label_fr' => 'Go-to-market santé', 'label_en' => 'Healthcare go-to-market', 'keywords' => 'gtm sante'],
                    ['label_fr' => 'Positionnement solution', 'label_en' => 'Solution positioning', 'keywords' => 'positionnement messaging'],
                    ['label_fr' => 'Sales enablement', 'label_en' => 'Sales enablement', 'keywords' => 'enablement ventes'],
                    ['label_fr' => 'Solutions B2B santé', 'label_en' => 'B2B health solutions', 'keywords' => 'b2b logiciel sante'],
                ],
            ],
            [
                'slug' => 'healthcare-content-manager',
                'name_fr' => 'Chargé(e) de Communication en Santé (Healthcare Content Manager)',
                'name_en' => 'Healthcare Communications / Content Manager',
                'suggestions' => [
                    ['label_fr' => 'Communication institutionnelle', 'label_en' => 'Institutional communications', 'keywords' => 'communication etablissement'],
                    ['label_fr' => 'Contenu médical / patient', 'label_en' => 'Medical / patient content', 'keywords' => 'contenu patient education'],
                    ['label_fr' => 'Rédaction scientifique', 'label_en' => 'Medical writing', 'keywords' => 'redaction scientifique'],
                    ['label_fr' => 'Relations presse santé', 'label_en' => 'Healthcare PR', 'keywords' => 'rp presse sante'],
                ],
            ],
            [
                'slug' => 'health-data-biostatistician',
                'name_fr' => 'Data Manager / Biostatisticien(ne) en Santé',
                'name_en' => 'Health Data Manager / Biostatistician',
                'suggestions' => [
                    ['label_fr' => 'Data management essais cliniques', 'label_en' => 'Clinical trial data management', 'keywords' => 'data management edc'],
                    ['label_fr' => 'Biostatistique', 'label_en' => 'Biostatistics', 'keywords' => 'biostatistique sas r'],
                    ['label_fr' => 'Analyse données de santé', 'label_en' => 'Health data analysis', 'keywords' => 'donnees sante analyse'],
                    ['label_fr' => 'CDISC / standards cliniques', 'label_en' => 'CDISC / clinical standards', 'keywords' => 'cdisc sdtm adam'],
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
                'slug' => 'full-stack-developer',
                'name_fr' => 'Développeur Full-Stack / Ingénieur Logiciel',
                'name_en' => 'Full-Stack Developer / Software Engineer',
                'suggestions' => [
                    ['label_fr' => 'Full-Stack', 'label_en' => 'Full-Stack', 'keywords' => 'fullstack full stack polyvalent'],
                    ['label_fr' => 'Frontend', 'label_en' => 'Frontend', 'keywords' => 'react vue angular ui'],
                    ['label_fr' => 'Backend', 'label_en' => 'Backend', 'keywords' => 'api microservices php node'],
                    ['label_fr' => 'PHP / Laravel', 'label_en' => 'PHP / Laravel', 'keywords' => 'laravel symfony php'],
                    ['label_fr' => 'JavaScript / TypeScript', 'label_en' => 'JavaScript / TypeScript', 'keywords' => 'javascript typescript node react'],
                ],
            ],
            [
                'slug' => 'software-architect',
                'name_fr' => 'Architecte Logiciel / Solutions',
                'name_en' => 'Software / Solutions Architect',
                'suggestions' => [
                    ['label_fr' => 'Architecture microservices', 'label_en' => 'Microservices architecture', 'keywords' => 'microservices distributed'],
                    ['label_fr' => 'Architecture cloud', 'label_en' => 'Cloud architecture', 'keywords' => 'aws azure gcp cloud native'],
                    ['label_fr' => 'Architecture entreprise', 'label_en' => 'Enterprise architecture', 'keywords' => 'ea urbanism si'],
                    ['label_fr' => 'Solutions techniques', 'label_en' => 'Technical solutions', 'keywords' => 'presales cadrage'],
                ],
            ],
            [
                'slug' => 'devops-sre',
                'name_fr' => 'Ingénieur DevOps / SRE',
                'name_en' => 'DevOps / SRE Engineer',
                'suggestions' => [
                    ['label_fr' => 'CI/CD', 'label_en' => 'CI/CD', 'keywords' => 'jenkins gitlab github actions'],
                    ['label_fr' => 'Kubernetes', 'label_en' => 'Kubernetes', 'keywords' => 'k8s docker containers helm'],
                    ['label_fr' => 'Infrastructure as Code', 'label_en' => 'Infrastructure as Code', 'keywords' => 'terraform ansible pulumi'],
                    ['label_fr' => 'SRE / observabilité', 'label_en' => 'SRE / observability', 'keywords' => 'monitoring prometheus grafana sre'],
                ],
            ],
            [
                'slug' => 'sysadmin-network',
                'name_fr' => 'Administrateur Systèmes et Réseaux',
                'name_en' => 'Systems & Network Administrator',
                'suggestions' => [
                    ['label_fr' => 'Administration Linux', 'label_en' => 'Linux administration', 'keywords' => 'linux debian redhat'],
                    ['label_fr' => 'Administration Windows', 'label_en' => 'Windows administration', 'keywords' => 'windows server active directory'],
                    ['label_fr' => 'Réseaux', 'label_en' => 'Networking', 'keywords' => 'cisco firewall vpn'],
                    ['label_fr' => 'Virtualisation', 'label_en' => 'Virtualization', 'keywords' => 'vmware hyper-v virtualisation'],
                ],
            ],
            [
                'slug' => 'cybersecurity-soc',
                'name_fr' => 'Ingénieur en Cybersécurité / Analyste SOC',
                'name_en' => 'Cybersecurity Engineer / SOC Analyst',
                'suggestions' => [
                    ['label_fr' => 'Analyste SOC', 'label_en' => 'SOC analyst', 'keywords' => 'soc siem monitoring'],
                    ['label_fr' => 'Pentest / audit', 'label_en' => 'Penetration testing / audit', 'keywords' => 'pentest audit securite'],
                    ['label_fr' => 'GRC / conformité', 'label_en' => 'GRC / compliance', 'keywords' => 'iso27001 rgpd conformite'],
                    ['label_fr' => 'Sécurité cloud', 'label_en' => 'Cloud security', 'keywords' => 'cspm cloud securite'],
                ],
            ],
            [
                'slug' => 'it-project-manager',
                'name_fr' => 'Chef de Projet Informatique',
                'name_en' => 'IT Project Manager',
                'suggestions' => [
                    ['label_fr' => 'Projet SI', 'label_en' => 'IT project', 'keywords' => 'projet si delivery'],
                    ['label_fr' => 'Agile / hybride', 'label_en' => 'Agile / hybrid', 'keywords' => 'agile scrum kanban'],
                    ['label_fr' => 'MOA / MOE', 'label_en' => 'Business / IT project lead', 'keywords' => 'moa moe cadrage'],
                    ['label_fr' => 'Transformation digitale', 'label_en' => 'Digital transformation', 'keywords' => 'transformation digitale'],
                ],
            ],
            [
                'slug' => 'product-owner',
                'name_fr' => 'Product Owner (PO)',
                'name_en' => 'Product Owner (PO)',
                'suggestions' => [
                    ['label_fr' => 'Backlog / user stories', 'label_en' => 'Backlog / user stories', 'keywords' => 'backlog user stories agile'],
                    ['label_fr' => 'Produit B2B', 'label_en' => 'B2B product', 'keywords' => 'b2b saas entreprise'],
                    ['label_fr' => 'Produit SaaS', 'label_en' => 'SaaS product', 'keywords' => 'saas subscription'],
                    ['label_fr' => 'Discovery produit', 'label_en' => 'Product discovery', 'keywords' => 'discovery research ux'],
                ],
            ],
            [
                'slug' => 'scrum-master',
                'name_fr' => 'Scrum Master',
                'name_en' => 'Scrum Master',
                'suggestions' => [
                    ['label_fr' => 'Scrum', 'label_en' => 'Scrum', 'keywords' => 'scrum ceremonies sprint'],
                    ['label_fr' => 'Kanban', 'label_en' => 'Kanban', 'keywords' => 'kanban flow'],
                    ['label_fr' => 'Coaching agile', 'label_en' => 'Agile coaching', 'keywords' => 'coaching agile transformation'],
                    ['label_fr' => 'Facilitation d\'équipe', 'label_en' => 'Team facilitation', 'keywords' => 'facilitation equipe'],
                ],
            ],
            [
                'slug' => 'cio-dsi',
                'name_fr' => 'Directeur des Systèmes d\'Information (DSI)',
                'name_en' => 'Chief Information Officer (CIO)',
                'suggestions' => [
                    ['label_fr' => 'DSI PME / ETI', 'label_en' => 'SME / mid-market CIO', 'keywords' => 'dsi pme eti'],
                    ['label_fr' => 'Stratégie SI', 'label_en' => 'IT strategy', 'keywords' => 'strategie si roadmap'],
                    ['label_fr' => 'Gouvernance IT', 'label_en' => 'IT governance', 'keywords' => 'gouvernance itil cobit'],
                    ['label_fr' => 'Pilotage budget SI', 'label_en' => 'IT budget management', 'keywords' => 'budget si cio'],
                ],
            ],
            [
                'slug' => 'it-business-developer',
                'name_fr' => 'Ingénieur d\'Affaires / Business Developer IT',
                'name_en' => 'IT Business Developer',
                'suggestions' => [
                    ['label_fr' => 'Prospection B2B', 'label_en' => 'B2B prospecting', 'keywords' => 'prospection b2b leads'],
                    ['label_fr' => 'Partenariats tech', 'label_en' => 'Tech partnerships', 'keywords' => 'partenariats alliances'],
                    ['label_fr' => 'ESN / intégrateur', 'label_en' => 'IT services / integrator', 'keywords' => 'esn integrateur ssii'],
                    ['label_fr' => 'Appels d\'offres', 'label_en' => 'RFP / tenders', 'keywords' => 'ao appels offres'],
                ],
            ],
            [
                'slug' => 'account-manager',
                'name_fr' => 'Account Manager / Responsable de Comptes',
                'name_en' => 'Account Manager',
                'suggestions' => [
                    ['label_fr' => 'Grands comptes', 'label_en' => 'Key accounts', 'keywords' => 'key account grands comptes'],
                    ['label_fr' => 'Renouvellement / upsell', 'label_en' => 'Renewal / upsell', 'keywords' => 'renewal upsell cross sell'],
                    ['label_fr' => 'Relation client IT', 'label_en' => 'IT client relationship', 'keywords' => 'relation client suivi'],
                    ['label_fr' => 'SaaS / abonnement', 'label_en' => 'SaaS / subscription', 'keywords' => 'saas subscription mrr'],
                ],
            ],
            [
                'slug' => 'sales-director',
                'name_fr' => 'Directeur Commercial',
                'name_en' => 'Sales Director',
                'suggestions' => [
                    ['label_fr' => 'Direction commerciale', 'label_en' => 'Sales leadership', 'keywords' => 'direction commerciale management'],
                    ['label_fr' => 'Sales B2B tech', 'label_en' => 'B2B tech sales', 'keywords' => 'b2b tech vente'],
                    ['label_fr' => 'Pipeline / objectifs', 'label_en' => 'Pipeline / targets', 'keywords' => 'pipeline crm objectifs'],
                    ['label_fr' => 'Équipe commerciale', 'label_en' => 'Sales team management', 'keywords' => 'equipe commerciale coaching'],
                ],
            ],
            [
                'slug' => 'partnerships-channel-manager',
                'name_fr' => 'Responsable des Partenariats / Channel Manager',
                'name_en' => 'Partnerships / Channel Manager',
                'suggestions' => [
                    ['label_fr' => 'Channel / revendeurs', 'label_en' => 'Channel / resellers', 'keywords' => 'channel revendeurs distributeurs'],
                    ['label_fr' => 'Alliances stratégiques', 'label_en' => 'Strategic alliances', 'keywords' => 'alliances partenariats'],
                    ['label_fr' => 'Partenariats cloud', 'label_en' => 'Cloud partnerships', 'keywords' => 'aws partner azure partner'],
                    ['label_fr' => 'Co-marketing', 'label_en' => 'Co-marketing', 'keywords' => 'co-marketing co-selling'],
                ],
            ],
            [
                'slug' => 'growth-hacker',
                'name_fr' => 'Growth Hacker',
                'name_en' => 'Growth Hacker',
                'suggestions' => [
                    ['label_fr' => 'Acquisition', 'label_en' => 'Acquisition', 'keywords' => 'acquisition leads growth'],
                    ['label_fr' => 'A/B testing', 'label_en' => 'A/B testing', 'keywords' => 'ab test experimentation'],
                    ['label_fr' => 'Growth SaaS', 'label_en' => 'SaaS growth', 'keywords' => 'saas plg activation'],
                    ['label_fr' => 'Funnel / conversion', 'label_en' => 'Funnel / conversion', 'keywords' => 'funnel conversion cro'],
                ],
            ],
            [
                'slug' => 'digital-marketing-manager',
                'name_fr' => 'Responsable Marketing Digital',
                'name_en' => 'Digital Marketing Manager',
                'suggestions' => [
                    ['label_fr' => 'SEO / SEA', 'label_en' => 'SEO / SEA', 'keywords' => 'seo sea google ads'],
                    ['label_fr' => 'Social ads', 'label_en' => 'Social advertising', 'keywords' => 'facebook linkedin ads social'],
                    ['label_fr' => 'Marketing automation', 'label_en' => 'Marketing automation', 'keywords' => 'hubspot marketing automation'],
                    ['label_fr' => 'Inbound marketing', 'label_en' => 'Inbound marketing', 'keywords' => 'inbound content leads'],
                ],
            ],
            [
                'slug' => 'product-marketing-manager',
                'name_fr' => 'Product Marketing Manager (PMM)',
                'name_en' => 'Product Marketing Manager (PMM)',
                'suggestions' => [
                    ['label_fr' => 'Go-to-market', 'label_en' => 'Go-to-market', 'keywords' => 'gtm lancement produit'],
                    ['label_fr' => 'Positionnement produit', 'label_en' => 'Product positioning', 'keywords' => 'positionnement messaging'],
                    ['label_fr' => 'Sales enablement', 'label_en' => 'Sales enablement', 'keywords' => 'enablement sales kit'],
                    ['label_fr' => 'Analyse marché', 'label_en' => 'Market analysis', 'keywords' => 'market research competitive'],
                ],
            ],
            [
                'slug' => 'content-manager',
                'name_fr' => 'Content Manager / Rédacteur Technique',
                'name_en' => 'Content Manager / Technical Writer',
                'suggestions' => [
                    ['label_fr' => 'Rédaction technique', 'label_en' => 'Technical writing', 'keywords' => 'documentation technique api'],
                    ['label_fr' => 'Documentation produit', 'label_en' => 'Product documentation', 'keywords' => 'docs knowledge base'],
                    ['label_fr' => 'Content SEO', 'label_en' => 'SEO content', 'keywords' => 'seo contenu editorial'],
                    ['label_fr' => 'Stratégie éditoriale', 'label_en' => 'Editorial strategy', 'keywords' => 'editorial calendrier contenu'],
                ],
            ],
            [
                'slug' => 'data-engineer',
                'name_fr' => 'Data Engineer',
                'name_en' => 'Data Engineer',
                'suggestions' => [
                    ['label_fr' => 'Pipeline ETL', 'label_en' => 'ETL pipeline', 'keywords' => 'etl pipeline ingestion'],
                    ['label_fr' => 'Spark / Big Data', 'label_en' => 'Spark / Big Data', 'keywords' => 'spark hadoop big data'],
                    ['label_fr' => 'Data warehouse', 'label_en' => 'Data warehouse', 'keywords' => 'dwh snowflake bigquery'],
                    ['label_fr' => 'dbt / orchestration', 'label_en' => 'dbt / orchestration', 'keywords' => 'dbt airflow orchestration'],
                ],
            ],
            [
                'slug' => 'data-scientist',
                'name_fr' => 'Data Scientist / Ingénieur IA',
                'name_en' => 'Data Scientist / AI Engineer',
                'suggestions' => [
                    ['label_fr' => 'Machine learning', 'label_en' => 'Machine learning', 'keywords' => 'ml scikit-learn tensorflow'],
                    ['label_fr' => 'Deep learning', 'label_en' => 'Deep learning', 'keywords' => 'deep learning pytorch'],
                    ['label_fr' => 'MLOps / IA générative', 'label_en' => 'MLOps / generative AI', 'keywords' => 'mlops llm genai'],
                    ['label_fr' => 'Analyse prédictive', 'label_en' => 'Predictive analytics', 'keywords' => 'predictive analytics modeles'],
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
                'slug' => 'construction-project-director',
                'name_fr' => 'Directeur de Projet / Directeur de Travaux',
                'name_en' => 'Project Director / Construction Director',
                'suggestions' => [
                    ['label_fr' => 'Direction de projet', 'label_en' => 'Project leadership', 'keywords' => 'projet chantier direction'],
                    ['label_fr' => 'Opération immobilière', 'label_en' => 'Real-estate development', 'keywords' => 'operation immobiliere'],
                    ['label_fr' => 'Pilotage multi-lots', 'label_en' => 'Multi-trade coordination', 'keywords' => 'lots entreprises'],
                    ['label_fr' => 'MOA / MOE', 'label_en' => 'Client / contractor side', 'keywords' => 'moa moe'],
                ],
            ],
            [
                'slug' => 'quantity-surveyor',
                'name_fr' => 'Ingénieur Études de Prix / Estimateur',
                'name_en' => 'Quantity Surveyor / Estimator',
                'suggestions' => [
                    ['label_fr' => 'Études de prix', 'label_en' => 'Cost estimating', 'keywords' => 'etudes prix metrage'],
                    ['label_fr' => 'Appels d\'offres', 'label_en' => 'Tenders / RFPs', 'keywords' => 'ao dpq'],
                    ['label_fr' => 'Métré / DPGF', 'label_en' => 'Bills of quantities', 'keywords' => 'metre dpgf'],
                    ['label_fr' => 'Chiffrage gros œuvre', 'label_en' => 'Structural works costing', 'keywords' => 'gros oeuvre chiffrage'],
                ],
            ],
            [
                'slug' => 'site-manager',
                'name_fr' => 'Conducteur de Travaux Principal / Ingénieur Travaux',
                'name_en' => 'Senior Site Manager / Works Engineer',
                'suggestions' => [
                    ['label_fr' => 'Bâtiment', 'label_en' => 'Building works', 'keywords' => 'batiment chantier'],
                    ['label_fr' => 'VRD / infrastructure', 'label_en' => 'Civil infrastructure', 'keywords' => 'vrd infrastructure'],
                    ['label_fr' => 'Planning / OPC', 'label_en' => 'Planning / scheduling', 'keywords' => 'planning opc'],
                    ['label_fr' => 'Coordination entreprises', 'label_en' => 'Trade coordination', 'keywords' => 'entreprises lots'],
                ],
            ],
            [
                'slug' => 'structural-engineer',
                'name_fr' => 'Ingénieur Structures / Béton Armé (Bureau d\'Études)',
                'name_en' => 'Structural / Reinforced Concrete Engineer',
                'suggestions' => [
                    ['label_fr' => 'Béton armé', 'label_en' => 'Reinforced concrete', 'keywords' => 'beton arme bael'],
                    ['label_fr' => 'Calcul de structures', 'label_en' => 'Structural analysis', 'keywords' => 'calcul structure'],
                    ['label_fr' => 'Bureau d\'études', 'label_en' => 'Design office', 'keywords' => 'bureau etudes'],
                    ['label_fr' => 'Acier / mixte', 'label_en' => 'Steel / composite', 'keywords' => 'charpente metallique'],
                ],
            ],
            [
                'slug' => 'hvac-fluids-engineer',
                'name_fr' => 'Ingénieur Fluides / CVC (Chauffage, Ventilation, Climatisation)',
                'name_en' => 'MEP / HVAC Engineer',
                'suggestions' => [
                    ['label_fr' => 'CVC', 'label_en' => 'HVAC', 'keywords' => 'cvc climatisation'],
                    ['label_fr' => 'Plomberie / sanitaire', 'label_en' => 'Plumbing / sanitary', 'keywords' => 'plomberie fluides'],
                    ['label_fr' => 'Électricité / CFO-CFA', 'label_en' => 'Electrical / low voltage', 'keywords' => 'cfo cfa'],
                    ['label_fr' => 'Études fluides', 'label_en' => 'MEP design', 'keywords' => 'etudes fluides'],
                ],
            ],
            [
                'slug' => 'geotechnical-engineer',
                'name_fr' => 'Ingénieur Géotechnique / Sols',
                'name_en' => 'Geotechnical / Soils Engineer',
                'suggestions' => [
                    ['label_fr' => 'Études de sols', 'label_en' => 'Soil investigation', 'keywords' => 'sols geotechnique'],
                    ['label_fr' => 'Fondations', 'label_en' => 'Foundations', 'keywords' => 'fondations pieux'],
                    ['label_fr' => 'Terrassements', 'label_en' => 'Earthworks', 'keywords' => 'terrassement'],
                    ['label_fr' => 'Stabilité / GTR', 'label_en' => 'Stability / GTR', 'keywords' => 'gtr stabilite'],
                ],
            ],
            [
                'slug' => 'construction-qse-manager',
                'name_fr' => 'Responsable Qualité, Sécurité, Environnement (QSE) - BTP',
                'name_en' => 'QSE Manager - Construction',
                'suggestions' => [
                    ['label_fr' => 'Sécurité chantier', 'label_en' => 'Site safety', 'keywords' => 'ppsps securite'],
                    ['label_fr' => 'Qualité BTP', 'label_en' => 'Construction quality', 'keywords' => 'qualite chantier'],
                    ['label_fr' => 'Environnement chantier', 'label_en' => 'Site environment', 'keywords' => 'environnement dechets'],
                    ['label_fr' => 'Coordination SPS', 'label_en' => 'Health & safety coordination', 'keywords' => 'sps coordinateur'],
                ],
            ],
            [
                'slug' => 'bim-manager',
                'name_fr' => 'Coordinateur BIM / BIM Manager',
                'name_en' => 'BIM Coordinator / BIM Manager',
                'suggestions' => [
                    ['label_fr' => 'BIM Manager', 'label_en' => 'BIM Manager', 'keywords' => 'bim revit'],
                    ['label_fr' => 'Coordination maquettes', 'label_en' => 'Model coordination', 'keywords' => 'clash detection navisworks'],
                    ['label_fr' => 'Revit / IFC', 'label_en' => 'Revit / IFC', 'keywords' => 'revit ifc'],
                    ['label_fr' => 'Process BIM', 'label_en' => 'BIM process', 'keywords' => 'convention bim lod'],
                ],
            ],
            [
                'slug' => 'site-logistics-manager',
                'name_fr' => 'Responsable du Matériel et de la Logistique de Chantier',
                'name_en' => 'Site Plant & Logistics Manager',
                'suggestions' => [
                    ['label_fr' => 'Matériel de chantier', 'label_en' => 'Construction plant', 'keywords' => 'materiel engins'],
                    ['label_fr' => 'Logistique chantier', 'label_en' => 'Site logistics', 'keywords' => 'logistique approvisionnement'],
                    ['label_fr' => 'Base vie', 'label_en' => 'Site compound', 'keywords' => 'base vie'],
                    ['label_fr' => 'Location d\'engins', 'label_en' => 'Plant hire', 'keywords' => 'location engins'],
                ],
            ],
            [
                'slug' => 'architect',
                'name_fr' => 'Architecte / Urbaniste (en Cabinet ou Entreprise Générale)',
                'name_en' => 'Architect / Urban Planner',
                'suggestions' => [
                    ['label_fr' => 'Conception architecturale', 'label_en' => 'Architectural design', 'keywords' => 'conception permis'],
                    ['label_fr' => 'Urbanisme', 'label_en' => 'Urban planning', 'keywords' => 'urbanisme'],
                    ['label_fr' => 'Suivi de chantier', 'label_en' => 'Site supervision', 'keywords' => 'suivi chantier'],
                    ['label_fr' => 'Entreprise générale', 'label_en' => 'General contractor', 'keywords' => 'entreprise generale'],
                ],
            ],
            [
                'slug' => 'construction-business-developer',
                'name_fr' => 'Ingénieur d\'Affaires / Responsable Développement - BTP',
                'name_en' => 'Business Developer - Construction',
                'suggestions' => [
                    ['label_fr' => 'Développement commercial BTP', 'label_en' => 'Construction business development', 'keywords' => 'affaires btp'],
                    ['label_fr' => 'Appels d\'offres', 'label_en' => 'Tenders', 'keywords' => 'ao marche public'],
                    ['label_fr' => 'Relation maîtres d\'ouvrage', 'label_en' => 'Client relationships', 'keywords' => 'moa promoteurs'],
                    ['label_fr' => 'Négociation marchés', 'label_en' => 'Contract negotiation', 'keywords' => 'marches negociation'],
                ],
            ],
            [
                'slug' => 'construction-sales-director',
                'name_fr' => 'Directeur Commercial - Construction et Immobilier',
                'name_en' => 'Sales Director - Construction & Real Estate',
                'suggestions' => [
                    ['label_fr' => 'Direction commerciale BTP', 'label_en' => 'Construction sales leadership', 'keywords' => 'direction commerciale'],
                    ['label_fr' => 'Promotion immobilière', 'label_en' => 'Property development', 'keywords' => 'promotion immobiliere'],
                    ['label_fr' => 'Équipe commerciale', 'label_en' => 'Sales team', 'keywords' => 'equipe commerciale'],
                    ['label_fr' => 'Marchés publics / privés', 'label_en' => 'Public / private markets', 'keywords' => 'marches publics'],
                ],
            ],
            [
                'slug' => 'construction-kam',
                'name_fr' => 'Responsable Grands Comptes (Key Account Manager) - Matériaux et BTP',
                'name_en' => 'Key Account Manager - Building Materials & Construction',
                'suggestions' => [
                    ['label_fr' => 'Grands comptes BTP', 'label_en' => 'Construction key accounts', 'keywords' => 'kam btp'],
                    ['label_fr' => 'Matériaux de construction', 'label_en' => 'Building materials', 'keywords' => 'materiaux'],
                    ['label_fr' => 'Négociation volume', 'label_en' => 'Volume negotiation', 'keywords' => 'volume contrats'],
                    ['label_fr' => 'Relation enseignes / majors', 'label_en' => 'Major contractors', 'keywords' => 'majors eg'],
                ],
            ],
            [
                'slug' => 'materials-prescriber',
                'name_fr' => 'Chargé de Prescription / Technico-Commercial B2B (Matériaux)',
                'name_en' => 'Specification / B2B Technical Sales (Materials)',
                'suggestions' => [
                    ['label_fr' => 'Prescription architectes', 'label_en' => 'Architect specification', 'keywords' => 'prescription architectes'],
                    ['label_fr' => 'Technico-commercial', 'label_en' => 'Technical sales', 'keywords' => 'technico commercial'],
                    ['label_fr' => 'Matériaux / systèmes', 'label_en' => 'Materials / systems', 'keywords' => 'systemes constructifs'],
                    ['label_fr' => 'Visite de chantiers', 'label_en' => 'Site visits', 'keywords' => 'visite chantiers'],
                ],
            ],
            [
                'slug' => 'construction-subcontracting',
                'name_fr' => 'Responsable des Partenariats et Sous-Traitance (Achats BTP)',
                'name_en' => 'Partnerships & Subcontracting Manager (Construction Procurement)',
                'suggestions' => [
                    ['label_fr' => 'Sous-traitance chantier', 'label_en' => 'Site subcontracting', 'keywords' => 'sous-traitance'],
                    ['label_fr' => 'Achats BTP', 'label_en' => 'Construction procurement', 'keywords' => 'achats btp'],
                    ['label_fr' => 'Consultation entreprises', 'label_en' => 'Trade tenders', 'keywords' => 'consultation lots'],
                    ['label_fr' => 'Partenariats locaux', 'label_en' => 'Local partnerships', 'keywords' => 'partenariats'],
                ],
            ],
            [
                'slug' => 'construction-marketing',
                'name_fr' => 'Responsable Marketing et Communication - BTP / Immobilier',
                'name_en' => 'Marketing & Communications Manager - Construction / Real Estate',
                'suggestions' => [
                    ['label_fr' => 'Communication BTP', 'label_en' => 'Construction communications', 'keywords' => 'communication btp'],
                    ['label_fr' => 'Marketing immobilier', 'label_en' => 'Real-estate marketing', 'keywords' => 'marketing immobilier'],
                    ['label_fr' => 'Marque employeur chantier', 'label_en' => 'Employer branding', 'keywords' => 'marque employeur'],
                    ['label_fr' => 'Supports commerciaux', 'label_en' => 'Sales collateral', 'keywords' => 'supports commerciaux'],
                ],
            ],
            [
                'slug' => 'construction-growth-leads',
                'name_fr' => 'Growth Hacker / Responsable Génération de Leads - Construction',
                'name_en' => 'Growth Hacker / Lead Generation - Construction',
                'suggestions' => [
                    ['label_fr' => 'Leads BTP / immobilier', 'label_en' => 'Construction / real-estate leads', 'keywords' => 'leads btp'],
                    ['label_fr' => 'Inbound B2B', 'label_en' => 'B2B inbound', 'keywords' => 'inbound seo'],
                    ['label_fr' => 'Campagnes digitales', 'label_en' => 'Digital campaigns', 'keywords' => 'ads linkedin'],
                    ['label_fr' => 'Funnel commercial', 'label_en' => 'Sales funnel', 'keywords' => 'funnel conversion'],
                ],
            ],
            [
                'slug' => 'assistant-site-manager',
                'name_fr' => 'Conducteur de Travaux Junior / Assistant Conducteur de Travaux',
                'name_en' => 'Junior Site Manager / Assistant Site Manager',
                'suggestions' => [
                    ['label_fr' => 'Assistanat travaux', 'label_en' => 'Works assistance', 'keywords' => 'assistant cdt'],
                    ['label_fr' => 'Suivi d\'exécution', 'label_en' => 'Works follow-up', 'keywords' => 'execution chantier'],
                    ['label_fr' => 'Compte-rendu chantier', 'label_en' => 'Site reports', 'keywords' => 'cr chantier'],
                    ['label_fr' => 'Première expérience BTP', 'label_en' => 'Early-career construction', 'keywords' => 'junior btp'],
                ],
            ],
            [
                'slug' => 'site-foreman',
                'name_fr' => 'Chef de Chantier / Chef d\'Équipe',
                'name_en' => 'Site Foreman / Team Leader',
                'suggestions' => [
                    ['label_fr' => 'Chef de chantier', 'label_en' => 'Site foreman', 'keywords' => 'chef de chantier'],
                    ['label_fr' => 'Encadrement d\'équipe', 'label_en' => 'Crew leadership', 'keywords' => 'equipe ouvriers'],
                    ['label_fr' => 'Gros œuvre', 'label_en' => 'Structural works', 'keywords' => 'gros oeuvre'],
                    ['label_fr' => 'Second œuvre', 'label_en' => 'Finishing trades', 'keywords' => 'second oeuvre'],
                ],
            ],
            [
                'slug' => 'mason-formworker',
                'name_fr' => 'Maçon / Coffreur-Bancheur',
                'name_en' => 'Mason / Formworker',
                'suggestions' => [
                    ['label_fr' => 'Maçonnerie', 'label_en' => 'Masonry', 'keywords' => 'maconnerie'],
                    ['label_fr' => 'Coffrage / banche', 'label_en' => 'Formwork', 'keywords' => 'coffrage banche'],
                    ['label_fr' => 'Béton', 'label_en' => 'Concrete', 'keywords' => 'beton coulage'],
                    ['label_fr' => 'Rénovation', 'label_en' => 'Renovation', 'keywords' => 'renovation'],
                ],
            ],
            [
                'slug' => 'rebar-worker',
                'name_fr' => 'Ferrailleur',
                'name_en' => 'Rebar / Steel Fixer',
                'suggestions' => [
                    ['label_fr' => 'Ferraillage', 'label_en' => 'Rebar fixing', 'keywords' => 'ferraillage armatures'],
                    ['label_fr' => 'Lecture de plans ferraillage', 'label_en' => 'Rebar drawings', 'keywords' => 'plans ferraillage'],
                    ['label_fr' => 'Préfabrication', 'label_en' => 'Prefabrication', 'keywords' => 'prefa'],
                    ['label_fr' => 'Chantier gros œuvre', 'label_en' => 'Structural site works', 'keywords' => 'gros oeuvre'],
                ],
            ],
            [
                'slug' => 'crane-plant-operator',
                'name_fr' => 'Grutier / Conducteur d\'Engins de Chantiers',
                'name_en' => 'Crane / Construction Plant Operator',
                'suggestions' => [
                    ['label_fr' => 'Grue à tour', 'label_en' => 'Tower crane', 'keywords' => 'grue a tour caces'],
                    ['label_fr' => 'Engins de terrassement', 'label_en' => 'Earthmoving plant', 'keywords' => 'pelle chargeuse'],
                    ['label_fr' => 'CACES', 'label_en' => 'Plant licenses (CACES)', 'keywords' => 'caces'],
                    ['label_fr' => 'Manutention chantier', 'label_en' => 'Site handling', 'keywords' => 'manutention'],
                ],
            ],
            [
                'slug' => 'electrician',
                'name_fr' => 'Électricien Bâtiment / Technicien Courants Forts et Faibles',
                'name_en' => 'Building Electrician / Power & Low-Voltage Technician',
                'suggestions' => [
                    ['label_fr' => 'Courants forts', 'label_en' => 'Power / high current', 'keywords' => 'cfo tableaux'],
                    ['label_fr' => 'Courants faibles', 'label_en' => 'Low voltage', 'keywords' => 'cfa vdi'],
                    ['label_fr' => 'Chantier neuf', 'label_en' => 'New-build', 'keywords' => 'neuf'],
                    ['label_fr' => 'Rénovation électrique', 'label_en' => 'Electrical renovation', 'keywords' => 'renovation'],
                ],
            ],
            [
                'slug' => 'plumber',
                'name_fr' => 'Plombier / Chauffagiste / Installateur Sanitaire',
                'name_en' => 'Plumber / Heating / Sanitary Installer',
                'suggestions' => [
                    ['label_fr' => 'Plomberie sanitaire', 'label_en' => 'Sanitary plumbing', 'keywords' => 'sanitaire'],
                    ['label_fr' => 'Chauffage', 'label_en' => 'Heating', 'keywords' => 'chauffage chaudiere'],
                    ['label_fr' => 'Climatisation', 'label_en' => 'Air conditioning', 'keywords' => 'clim cvc'],
                    ['label_fr' => 'Installation neuve', 'label_en' => 'New installation', 'keywords' => 'installation'],
                ],
            ],
            [
                'slug' => 'painter-drywall',
                'name_fr' => 'Peintre en Bâtiment / Plaquiste / Jointoyeur',
                'name_en' => 'Painter / Drywaller / Taper',
                'suggestions' => [
                    ['label_fr' => 'Peinture bâtiment', 'label_en' => 'Building painting', 'keywords' => 'peinture'],
                    ['label_fr' => 'Plaquiste', 'label_en' => 'Drywall', 'keywords' => 'placo plaquiste'],
                    ['label_fr' => 'Jointoiement', 'label_en' => 'Taping / jointing', 'keywords' => 'joints'],
                    ['label_fr' => 'Finitions', 'label_en' => 'Finishes', 'keywords' => 'finitions'],
                ],
            ],
            [
                'slug' => 'carpenter-roofer',
                'name_fr' => 'Charpentier / Couvreur-Zingueur',
                'name_en' => 'Carpenter / Roofer-Zinc Worker',
                'suggestions' => [
                    ['label_fr' => 'Charpente bois', 'label_en' => 'Timber framing', 'keywords' => 'charpente bois'],
                    ['label_fr' => 'Couverture', 'label_en' => 'Roofing', 'keywords' => 'couverture tuiles'],
                    ['label_fr' => 'Zinguerie', 'label_en' => 'Zinc work', 'keywords' => 'zinguerie'],
                    ['label_fr' => 'Étanchéité', 'label_en' => 'Waterproofing', 'keywords' => 'etancheite'],
                ],
            ],
            [
                'slug' => 'surveyor-topographer',
                'name_fr' => 'Technicien Géomètre / Topographe',
                'name_en' => 'Surveyor / Topographer',
                'suggestions' => [
                    ['label_fr' => 'Topographie chantier', 'label_en' => 'Site surveying', 'keywords' => 'topo implantation'],
                    ['label_fr' => 'Implantation', 'label_en' => 'Setting-out', 'keywords' => 'implantation'],
                    ['label_fr' => 'Levé / GPS', 'label_en' => 'Survey / GPS', 'keywords' => 'leve gps station'],
                    ['label_fr' => 'VRD', 'label_en' => 'Civil works', 'keywords' => 'vrd'],
                ],
            ],
            [
                'slug' => 'cad-drafter',
                'name_fr' => 'Dessinateur / Projeteur Bâtiment (DAO / CAO)',
                'name_en' => 'CAD Drafter / Building Designer',
                'suggestions' => [
                    ['label_fr' => 'AutoCAD / DAO', 'label_en' => 'AutoCAD / CAD', 'keywords' => 'autocad dao'],
                    ['label_fr' => 'Revit / BIM', 'label_en' => 'Revit / BIM', 'keywords' => 'revit bim'],
                    ['label_fr' => 'Plans d\'exécution', 'label_en' => 'Shop drawings', 'keywords' => 'plans execution'],
                    ['label_fr' => 'Synthèse technique', 'label_en' => 'Technical coordination drawings', 'keywords' => 'synthese'],
                ],
            ],
            [
                'slug' => 'site-storekeeper',
                'name_fr' => 'Magasinier de Chantier / Gestionnaire de Stock',
                'name_en' => 'Site Storekeeper / Stock Controller',
                'suggestions' => [
                    ['label_fr' => 'Magasin chantier', 'label_en' => 'Site store', 'keywords' => 'magasin chantier'],
                    ['label_fr' => 'Gestion de stocks', 'label_en' => 'Stock control', 'keywords' => 'stocks'],
                    ['label_fr' => 'Réception matériaux', 'label_en' => 'Materials receiving', 'keywords' => 'reception materiaux'],
                    ['label_fr' => 'Outillage', 'label_en' => 'Tools', 'keywords' => 'outillage'],
                ],
            ],
            [
                'slug' => 'site-admin-assistant',
                'name_fr' => 'Assistant(e) Technique ou Administratif(ve) de Chantier',
                'name_en' => 'Site Technical / Administrative Assistant',
                'suggestions' => [
                    ['label_fr' => 'Secrétariat chantier', 'label_en' => 'Site administration', 'keywords' => 'secretariat chantier'],
                    ['label_fr' => 'DOE / documents', 'label_en' => 'Handover documents', 'keywords' => 'doe documents'],
                    ['label_fr' => 'Pointage / RH chantier', 'label_en' => 'Timesheets / site HR', 'keywords' => 'pointage'],
                    ['label_fr' => 'Suivi administratif', 'label_en' => 'Admin follow-up', 'keywords' => 'administratif'],
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
                'slug' => 'plant-director',
                'name_fr' => 'Directeur d\'Usine / Directeur de Site Industriel',
                'name_en' => 'Plant / Industrial Site Director',
                'suggestions' => [
                    ['label_fr' => 'Direction de site', 'label_en' => 'Site leadership', 'keywords' => 'usine site industriel'],
                    ['label_fr' => 'Pilotage P&L', 'label_en' => 'P&L management', 'keywords' => 'budget resultat site'],
                    ['label_fr' => 'Excellence opérationnelle', 'label_en' => 'Operational excellence', 'keywords' => 'kpi production performance'],
                    ['label_fr' => 'Management d\'équipes', 'label_en' => 'People management', 'keywords' => 'encadrement effectifs'],
                ],
            ],
            [
                'slug' => 'operations-director-coo',
                'name_fr' => 'Directeur des Opérations (COO)',
                'name_en' => 'Chief Operating Officer (COO)',
                'suggestions' => [
                    ['label_fr' => 'Direction des opérations', 'label_en' => 'Operations leadership', 'keywords' => 'coo operations'],
                    ['label_fr' => 'Multi-sites', 'label_en' => 'Multi-site operations', 'keywords' => 'multi sites usines'],
                    ['label_fr' => 'Supply chain & production', 'label_en' => 'Supply chain & production', 'keywords' => 'supply production'],
                    ['label_fr' => 'Transformation industrielle', 'label_en' => 'Industrial transformation', 'keywords' => 'transformation lean'],
                ],
            ],
            [
                'slug' => 'production-manager',
                'name_fr' => 'Responsable de Production / Chef d\'Atelier',
                'name_en' => 'Production Manager / Workshop Supervisor',
                'suggestions' => [
                    ['label_fr' => 'Chef d\'atelier', 'label_en' => 'Workshop supervisor', 'keywords' => 'atelier planning'],
                    ['label_fr' => 'Pilotage d\'équipe production', 'label_en' => 'Production team leadership', 'keywords' => 'equipe production'],
                    ['label_fr' => 'Planning / TRS', 'label_en' => 'Planning / OEE', 'keywords' => 'trs oee cadences'],
                    ['label_fr' => 'Sécurité atelier', 'label_en' => 'Shop-floor safety', 'keywords' => 'securite production'],
                ],
            ],
            [
                'slug' => 'methods-industrialization-engineer',
                'name_fr' => 'Ingénieur Méthodes et Industrialisation',
                'name_en' => 'Methods & Industrialization Engineer',
                'suggestions' => [
                    ['label_fr' => 'Industrialisation produit', 'label_en' => 'Product industrialization', 'keywords' => 'industrialisation npi'],
                    ['label_fr' => 'Gamme / temps de cycle', 'label_en' => 'Process routing / cycle time', 'keywords' => 'gamme temps cycle'],
                    ['label_fr' => 'Outillage / process', 'label_en' => 'Tooling / process', 'keywords' => 'outillage process'],
                    ['label_fr' => 'Lancement en production', 'label_en' => 'Production launch', 'keywords' => 'lancement serie'],
                ],
            ],
            [
                'slug' => 'lean-continuous-improvement',
                'name_fr' => 'Ingénieur Amélioration Continue (Lean / Six Sigma)',
                'name_en' => 'Continuous Improvement Engineer (Lean / Six Sigma)',
                'suggestions' => [
                    ['label_fr' => 'Lean manufacturing', 'label_en' => 'Lean manufacturing', 'keywords' => 'lean 5s kaizen'],
                    ['label_fr' => 'Six Sigma', 'label_en' => 'Six Sigma', 'keywords' => 'six sigma dmaic'],
                    ['label_fr' => 'Kaizen / 5S', 'label_en' => 'Kaizen / 5S', 'keywords' => 'kaizen 5s gemba'],
                    ['label_fr' => 'Réduction des coûts', 'label_en' => 'Cost reduction', 'keywords' => 'couts gaspillage muda'],
                ],
            ],
            [
                'slug' => 'maintenance-reliability-manager',
                'name_fr' => 'Responsable Maintenance et Fiabilité (TPM)',
                'name_en' => 'Maintenance & Reliability Manager (TPM)',
                'suggestions' => [
                    ['label_fr' => 'TPM', 'label_en' => 'TPM', 'keywords' => 'tpm fiabilite'],
                    ['label_fr' => 'Maintenance préventive', 'label_en' => 'Preventive maintenance', 'keywords' => 'preventive gmao'],
                    ['label_fr' => 'Fiabilité / MTBF', 'label_en' => 'Reliability / MTBF', 'keywords' => 'mtbf mttr fiabilite'],
                    ['label_fr' => 'GMAO', 'label_en' => 'CMMS', 'keywords' => 'gmao cmms'],
                ],
            ],
            [
                'slug' => 'qhse-manager',
                'name_fr' => 'Responsable Qualité, Hygiène, Sécurité, Environnement (QHSE)',
                'name_en' => 'QHSE Manager',
                'suggestions' => [
                    ['label_fr' => 'Qualité ISO', 'label_en' => 'ISO quality', 'keywords' => 'iso 9001 qualite'],
                    ['label_fr' => 'HSE / sécurité', 'label_en' => 'HSE / safety', 'keywords' => 'hse sst accident'],
                    ['label_fr' => 'Environnement ISO 14001', 'label_en' => 'Environment ISO 14001', 'keywords' => 'iso 14001 environnement'],
                    ['label_fr' => 'Audit / certification', 'label_en' => 'Audit / certification', 'keywords' => 'audit certification'],
                ],
            ],
            [
                'slug' => 'supply-chain-logistics-manager',
                'name_fr' => 'Ingénieur Supply Chain / Responsable Logistique',
                'name_en' => 'Supply Chain Engineer / Logistics Manager',
                'suggestions' => [
                    ['label_fr' => 'Supply chain industrielle', 'label_en' => 'Industrial supply chain', 'keywords' => 'supply chain s&op'],
                    ['label_fr' => 'Logistique interne', 'label_en' => 'Internal logistics', 'keywords' => 'entrepot flux internes'],
                    ['label_fr' => 'Planification MRP', 'label_en' => 'MRP planning', 'keywords' => 'mrp erp planification'],
                    ['label_fr' => 'Stocks / approvisionnements', 'label_en' => 'Inventory / procurement', 'keywords' => 'stocks appro'],
                ],
            ],
            [
                'slug' => 'industrial-buyer',
                'name_fr' => 'Responsable des Achats Industriels / Acheteur Industriel',
                'name_en' => 'Industrial Purchasing Manager / Buyer',
                'suggestions' => [
                    ['label_fr' => 'Achats matières', 'label_en' => 'Raw materials purchasing', 'keywords' => 'matieres premieres'],
                    ['label_fr' => 'Achats sous-traitance', 'label_en' => 'Subcontracting purchasing', 'keywords' => 'sous-traitance'],
                    ['label_fr' => 'Négociation fournisseurs', 'label_en' => 'Supplier negotiation', 'keywords' => 'negociation fournisseurs'],
                    ['label_fr' => 'Sourcing industriel', 'label_en' => 'Industrial sourcing', 'keywords' => 'sourcing panel'],
                ],
            ],
            [
                'slug' => 'maintenance-technician',
                'name_fr' => 'Technicien de Maintenance Industrielle / Automaticien',
                'name_en' => 'Industrial Maintenance Technician / Automation Technician',
                'suggestions' => [
                    ['label_fr' => 'Électromécanique', 'label_en' => 'Electromechanics', 'keywords' => 'electromecanique'],
                    ['label_fr' => 'Automatisme / PLC', 'label_en' => 'Automation / PLC', 'keywords' => 'automate plc siemens'],
                    ['label_fr' => 'Dépannage machines', 'label_en' => 'Machine troubleshooting', 'keywords' => 'depannage machines'],
                    ['label_fr' => 'Instrumentation', 'label_en' => 'Instrumentation', 'keywords' => 'capteurs regulation'],
                ],
            ],
            [
                'slug' => 'industrial-project-rd',
                'name_fr' => 'Chef de Projet Industriel / Ingénieur R&D',
                'name_en' => 'Industrial Project Manager / R&D Engineer',
                'suggestions' => [
                    ['label_fr' => 'Projet industriel', 'label_en' => 'Industrial project', 'keywords' => 'projet capex'],
                    ['label_fr' => 'R&D produit', 'label_en' => 'Product R&D', 'keywords' => 'r&d conception'],
                    ['label_fr' => 'NPI / industrialisation', 'label_en' => 'NPI / industrialization', 'keywords' => 'npi nouveau produit'],
                    ['label_fr' => 'Bureau d\'études', 'label_en' => 'Design office', 'keywords' => 'bureau etudes cao'],
                ],
            ],
            [
                'slug' => 'industrial-mechanical-energy-engineer',
                'name_fr' => 'Ingénieur Génie Mécanique & Énergétique',
                'name_en' => 'Mechanical & Energy Systems Engineer',
                'suggestions' => [
                    ['label_fr' => 'R&D & conception mécanique', 'label_en' => 'Mechanical design R&D', 'keywords' => 'conception mecanique r&d'],
                    ['label_fr' => 'NPI / industrialisation produit', 'label_en' => 'NPI / product industrialization', 'keywords' => 'npi industrialisation lancement'],
                    ['label_fr' => 'Bureau d\'études / CAO', 'label_en' => 'Design office / CAD', 'keywords' => 'cao solidworks catia bureau etudes'],
                    ['label_fr' => 'Simulation FEM / CFD', 'label_en' => 'FEM / CFD simulation', 'keywords' => 'fem cfd ansys simulation'],
                    ['label_fr' => 'Optimisation systèmes industriels', 'label_en' => 'Industrial systems optimization', 'keywords' => 'optimisation performance systeme'],
                    ['label_fr' => 'Projet industriel / équipements lourds', 'label_en' => 'Industrial project / heavy equipment', 'keywords' => 'equipement lourd projet industriel'],
                    ['label_fr' => 'Maintenance & fiabilité mécanique', 'label_en' => 'Mechanical maintenance & reliability', 'keywords' => 'fiabilite maintenance mecanique'],
                    ['label_fr' => 'Mécanique des fluides industriels', 'label_en' => 'Industrial fluid mechanics', 'keywords' => 'fluides hydraulique pneumatique'],
                ],
            ],
            [
                'slug' => 'industrial-business-developer',
                'name_fr' => 'Ingénieur d\'Affaires Industriel / Business Developer B2B',
                'name_en' => 'Industrial Business Developer / B2B Sales Engineer',
                'suggestions' => [
                    ['label_fr' => 'Prospection B2B', 'label_en' => 'B2B prospecting', 'keywords' => 'prospection b2b'],
                    ['label_fr' => 'Appels d\'offres industriels', 'label_en' => 'Industrial RFPs', 'keywords' => 'ao appels offres'],
                    ['label_fr' => 'Solutions industrielles', 'label_en' => 'Industrial solutions', 'keywords' => 'solutions techniques'],
                    ['label_fr' => 'Négociation contrats', 'label_en' => 'Contract negotiation', 'keywords' => 'contrats negociation'],
                ],
            ],
            [
                'slug' => 'industrial-kam',
                'name_fr' => 'Responsable Grands Comptes (Key Account Manager) - Industrie',
                'name_en' => 'Key Account Manager - Industry',
                'suggestions' => [
                    ['label_fr' => 'Grands comptes industriels', 'label_en' => 'Industrial key accounts', 'keywords' => 'kam grands comptes'],
                    ['label_fr' => 'Relation client long terme', 'label_en' => 'Long-term client relationship', 'keywords' => 'relation client'],
                    ['label_fr' => 'Renouvellement / upsell', 'label_en' => 'Renewal / upsell', 'keywords' => 'renewal upsell'],
                    ['label_fr' => 'Comptes OEM / équipementiers', 'label_en' => 'OEM / supplier accounts', 'keywords' => 'oem equipementier'],
                ],
            ],
            [
                'slug' => 'industrial-partnerships',
                'name_fr' => 'Responsable des Partenariats et Sous-traitance Industrielle',
                'name_en' => 'Industrial Partnerships & Subcontracting Manager',
                'suggestions' => [
                    ['label_fr' => 'Sous-traitance industrielle', 'label_en' => 'Industrial subcontracting', 'keywords' => 'sous-traitance'],
                    ['label_fr' => 'Partenariats usine', 'label_en' => 'Plant partnerships', 'keywords' => 'partenariats'],
                    ['label_fr' => 'Panel fournisseurs', 'label_en' => 'Supplier panel', 'keywords' => 'panel fournisseurs'],
                    ['label_fr' => 'Co-développement', 'label_en' => 'Co-development', 'keywords' => 'co-developpement'],
                ],
            ],
            [
                'slug' => 'industrial-sales-director',
                'name_fr' => 'Directeur Commercial Industriel',
                'name_en' => 'Industrial Sales Director',
                'suggestions' => [
                    ['label_fr' => 'Direction commerciale B2B', 'label_en' => 'B2B sales leadership', 'keywords' => 'direction commerciale'],
                    ['label_fr' => 'Équipe commerciale industrie', 'label_en' => 'Industrial sales team', 'keywords' => 'equipe commerciale'],
                    ['label_fr' => 'Pipeline / objectifs', 'label_en' => 'Pipeline / targets', 'keywords' => 'pipeline crm'],
                    ['label_fr' => 'Marchés export', 'label_en' => 'Export markets', 'keywords' => 'export international'],
                ],
            ],
            [
                'slug' => 'industrial-marketing-manager',
                'name_fr' => 'Responsable Marketing Industriel / B2B Product Marketing Manager',
                'name_en' => 'Industrial Marketing / B2B Product Marketing Manager',
                'suggestions' => [
                    ['label_fr' => 'Marketing B2B', 'label_en' => 'B2B marketing', 'keywords' => 'marketing b2b'],
                    ['label_fr' => 'Product marketing industriel', 'label_en' => 'Industrial product marketing', 'keywords' => 'pmm produit'],
                    ['label_fr' => 'Go-to-market', 'label_en' => 'Go-to-market', 'keywords' => 'gtm lancement'],
                    ['label_fr' => 'Études de marché', 'label_en' => 'Market research', 'keywords' => 'etudes marche'],
                ],
            ],
            [
                'slug' => 'industrial-growth-leads',
                'name_fr' => 'Growth Hacker / Responsable Génération de Leads Industriels',
                'name_en' => 'Growth Hacker / Industrial Lead Generation Manager',
                'suggestions' => [
                    ['label_fr' => 'Génération de leads B2B', 'label_en' => 'B2B lead generation', 'keywords' => 'leads inbound outbound'],
                    ['label_fr' => 'Inbound industriel', 'label_en' => 'Industrial inbound', 'keywords' => 'inbound content'],
                    ['label_fr' => 'ABM / LinkedIn', 'label_en' => 'ABM / LinkedIn', 'keywords' => 'abm linkedin'],
                    ['label_fr' => 'Funnel commercial', 'label_en' => 'Sales funnel', 'keywords' => 'funnel conversion'],
                ],
            ],
            [
                'slug' => 'industrial-content-manager',
                'name_fr' => 'Chargé de Communication B2B / Content Manager Industriel',
                'name_en' => 'B2B Communications / Industrial Content Manager',
                'suggestions' => [
                    ['label_fr' => 'Contenu technique B2B', 'label_en' => 'B2B technical content', 'keywords' => 'contenu technique'],
                    ['label_fr' => 'Communication industrielle', 'label_en' => 'Industrial communications', 'keywords' => 'communication usine'],
                    ['label_fr' => 'Documentation produit', 'label_en' => 'Product documentation', 'keywords' => 'fiches techniques'],
                    ['label_fr' => 'Salons / événements', 'label_en' => 'Trade shows / events', 'keywords' => 'salons evenements'],
                ],
            ],
            [
                'slug' => 'production-operator',
                'name_fr' => 'Opérateur de Production / Ligne de Fabrication',
                'name_en' => 'Production / Manufacturing Line Operator',
                'suggestions' => [
                    ['label_fr' => 'Ligne de fabrication', 'label_en' => 'Manufacturing line', 'keywords' => 'ligne fabrication'],
                    ['label_fr' => 'Travail en 2x8 / 3x8', 'label_en' => 'Shift work', 'keywords' => '2x8 3x8 postes'],
                    ['label_fr' => 'Agroalimentaire', 'label_en' => 'Agri-food', 'keywords' => 'agroalimentaire'],
                    ['label_fr' => 'Automobile', 'label_en' => 'Automotive', 'keywords' => 'automobile'],
                ],
            ],
            [
                'slug' => 'industrial-assembler',
                'name_fr' => 'Assembleur / Monteur Industriel',
                'name_en' => 'Industrial Assembler / Fitter',
                'suggestions' => [
                    ['label_fr' => 'Montage mécanique', 'label_en' => 'Mechanical assembly', 'keywords' => 'montage mecanique'],
                    ['label_fr' => 'Assemblage électrique', 'label_en' => 'Electrical assembly', 'keywords' => 'assemblage electrique'],
                    ['label_fr' => 'Postes d\'assemblage', 'label_en' => 'Assembly stations', 'keywords' => 'poste assemblage'],
                    ['label_fr' => 'Lecture de plans', 'label_en' => 'Blueprint reading', 'keywords' => 'plans nomenclatures'],
                ],
            ],
            [
                'slug' => 'machine-line-operator',
                'name_fr' => 'Conducteur de Ligne / de Machines',
                'name_en' => 'Line / Machine Operator',
                'suggestions' => [
                    ['label_fr' => 'Conduite de ligne', 'label_en' => 'Line driving', 'keywords' => 'conduite ligne'],
                    ['label_fr' => 'Réglage machines', 'label_en' => 'Machine setup', 'keywords' => 'reglage machines'],
                    ['label_fr' => 'CNC / machines-outils', 'label_en' => 'CNC / machine tools', 'keywords' => 'cnc machines outils'],
                    ['label_fr' => 'Surveillance process', 'label_en' => 'Process monitoring', 'keywords' => 'surveillance process'],
                ],
            ],
            [
                'slug' => 'warehouse-operator',
                'name_fr' => 'Cariste / Magasinier / Agent de Logistique',
                'name_en' => 'Forklift Operator / Warehouse Clerk / Logistics Agent',
                'suggestions' => [
                    ['label_fr' => 'Cariste CACES', 'label_en' => 'Forklift (CACES)', 'keywords' => 'caces chariot'],
                    ['label_fr' => 'Magasin / picking', 'label_en' => 'Warehouse / picking', 'keywords' => 'magasin picking'],
                    ['label_fr' => 'Réception / expédition', 'label_en' => 'Receiving / shipping', 'keywords' => 'reception expedition'],
                    ['label_fr' => 'Gestion de stocks', 'label_en' => 'Inventory handling', 'keywords' => 'stocks wms'],
                ],
            ],
            [
                'slug' => 'maintenance-operator',
                'name_fr' => 'Technicien de Maintenance (Niveau Exécutant)',
                'name_en' => 'Maintenance Technician (Shop-floor)',
                'suggestions' => [
                    ['label_fr' => 'Maintenance 1er niveau', 'label_en' => 'First-level maintenance', 'keywords' => 'maintenance 1er niveau'],
                    ['label_fr' => 'Dépannage simple', 'label_en' => 'Basic troubleshooting', 'keywords' => 'depannage'],
                    ['label_fr' => 'Graissage / contrôles', 'label_en' => 'Lubrication / checks', 'keywords' => 'graissage rondes'],
                    ['label_fr' => 'Support production', 'label_en' => 'Production support', 'keywords' => 'support production'],
                ],
            ],
            [
                'slug' => 'quality-controller',
                'name_fr' => 'Contrôleur / Vérificateur Qualité (Agent de Contrôle)',
                'name_en' => 'Quality Controller / Inspector',
                'suggestions' => [
                    ['label_fr' => 'Contrôle dimensionnel', 'label_en' => 'Dimensional inspection', 'keywords' => 'metrologie controle'],
                    ['label_fr' => 'Contrôle en ligne', 'label_en' => 'In-line inspection', 'keywords' => 'controle ligne'],
                    ['label_fr' => 'Non-conformités', 'label_en' => 'Non-conformities', 'keywords' => 'nc rebuts'],
                    ['label_fr' => 'Auto-contrôle', 'label_en' => 'Self-inspection', 'keywords' => 'autocontrôle'],
                ],
            ],
            [
                'slug' => 'welder-boilermaker',
                'name_fr' => 'Soudeur / Chaudronnier / Ajusteur-Manoeuvrier',
                'name_en' => 'Welder / Boilermaker / Fitter',
                'suggestions' => [
                    ['label_fr' => 'Soudure TIG / MIG', 'label_en' => 'TIG / MIG welding', 'keywords' => 'tig mig mag'],
                    ['label_fr' => 'Chaudronnerie', 'label_en' => 'Boilermaking', 'keywords' => 'chaudronnerie'],
                    ['label_fr' => 'Ajustage / montage', 'label_en' => 'Fitting / assembly', 'keywords' => 'ajustage montage'],
                    ['label_fr' => 'Lecture de plans chaudronnés', 'label_en' => 'Boilermaking drawings', 'keywords' => 'plans chaudronnerie'],
                ],
            ],
            [
                'slug' => 'industrial-facilities-maintenance',
                'name_fr' => 'Agent d\'Entretien et de Maintenance des Bâtiments Industriels',
                'name_en' => 'Industrial Facilities Maintenance Agent',
                'suggestions' => [
                    ['label_fr' => 'Entretien bâtiments', 'label_en' => 'Building maintenance', 'keywords' => 'batiments usine'],
                    ['label_fr' => 'Multiservices techniques', 'label_en' => 'Multi-trade facilities', 'keywords' => 'plomberie electricite'],
                    ['label_fr' => 'Espaces industriels', 'label_en' => 'Industrial premises', 'keywords' => 'locaux ateliers'],
                    ['label_fr' => 'Petits travaux', 'label_en' => 'Minor works', 'keywords' => 'travaux entretien'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'energy',
        'name_fr' => 'Énergie & environnement',
        'name_en' => 'Energy & environment',
        'professions' => [
            // ——— Direction & encadrement ———
            [
                'slug' => 'energy-operations-director',
                'name_fr' => 'Directeur / Responsable d\'exploitation énergie',
                'name_en' => 'Energy Operations Director / Manager',
                'suggestions' => [
                    ['label_fr' => 'Directeur d\'exploitation centrale / site', 'label_en' => 'Plant / site operations director', 'keywords' => 'exploitation centrale site'],
                    ['label_fr' => 'Directeur des opérations énergie', 'label_en' => 'Energy operations director', 'keywords' => 'operations energie'],
                    ['label_fr' => 'Responsable d\'unité de production', 'label_en' => 'Production unit manager', 'keywords' => 'unite production'],
                    ['label_fr' => 'Directeur technique énergie', 'label_en' => 'Energy technical director', 'keywords' => 'direction technique'],
                ],
            ],
            [
                'slug' => 'energy-project-director',
                'name_fr' => 'Directeur / Chef de projet énergie & environnement',
                'name_en' => 'Energy & Environment Project Director / Manager',
                'suggestions' => [
                    ['label_fr' => 'Directeur de projet EnR', 'label_en' => 'Renewables project director', 'keywords' => 'projet enr'],
                    ['label_fr' => 'Chef de projet solaire / éolien', 'label_en' => 'Solar / wind project manager', 'keywords' => 'projet solaire eolien'],
                    ['label_fr' => 'Chef de projet réseaux / utilities', 'label_en' => 'Grid / utilities project manager', 'keywords' => 'projet reseaux'],
                    ['label_fr' => 'PMO énergie / infrastructure', 'label_en' => 'Energy / infrastructure PMO', 'keywords' => 'pmo infrastructure'],
                    ['label_fr' => 'Chef de projet EPC / construction', 'label_en' => 'EPC / construction project manager', 'keywords' => 'epc construction'],
                ],
            ],
            [
                'slug' => 'energy-asset-manager',
                'name_fr' => 'Asset manager / Responsable de parc énergétique',
                'name_en' => 'Energy Asset / Portfolio Manager',
                'suggestions' => [
                    ['label_fr' => 'Asset manager EnR', 'label_en' => 'Renewables asset manager', 'keywords' => 'asset management enr'],
                    ['label_fr' => 'Responsable de parc solaire / éolien', 'label_en' => 'Solar / wind fleet manager', 'keywords' => 'parc solaire eolien'],
                    ['label_fr' => 'Performance / availability assets', 'label_en' => 'Asset performance / availability', 'keywords' => 'performance disponibilite'],
                    ['label_fr' => 'Optimisation O&M / contrats', 'label_en' => 'O&M optimisation / contracts', 'keywords' => 'om contrats'],
                ],
            ],

            // ——— Ingénierie énergie ———
            [
                'slug' => 'energy-engineer',
                'name_fr' => 'Ingénieur énergie / transition énergétique',
                'name_en' => 'Energy / Energy Transition Engineer',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur énergies renouvelables', 'label_en' => 'Renewable energy engineer', 'keywords' => 'solaire eolien photovoltaique'],
                    ['label_fr' => 'Ingénieur efficacité énergétique', 'label_en' => 'Energy efficiency engineer', 'keywords' => 'audit energetique'],
                    ['label_fr' => 'Transition énergétique / décarbonation', 'label_en' => 'Energy transition / decarbonization', 'keywords' => 'transition decarbonation'],
                    ['label_fr' => 'Réseaux & utilities', 'label_en' => 'Grids & utilities', 'keywords' => 'utilities reseau energie'],
                    ['label_fr' => 'Ingénieur études énergétiques', 'label_en' => 'Energy studies engineer', 'keywords' => 'etudes energetiques'],
                ],
            ],
            [
                'slug' => 'mechanical-energy-engineer',
                'name_fr' => 'Ingénieur génie mécanique & énergétique',
                'name_en' => 'Mechanical & Energy Systems Engineer',
                'suggestions' => [
                    ['label_fr' => 'Efficacité énergétique & décarbonation', 'label_en' => 'Energy efficiency & decarbonization', 'keywords' => 'efficacite decarbonation'],
                    ['label_fr' => 'Systèmes thermiques & fluides', 'label_en' => 'Thermal & fluid systems', 'keywords' => 'thermique fluides echangeurs'],
                    ['label_fr' => 'Turbomachines / équipements rotatifs', 'label_en' => 'Turbomachinery / rotating equipment', 'keywords' => 'turbine compresseur pompe'],
                    ['label_fr' => 'Simulation thermique / CFD', 'label_en' => 'Thermal simulation / CFD', 'keywords' => 'cfd simulation thermique'],
                    ['label_fr' => 'Audit & performance énergétique', 'label_en' => 'Energy audit & performance', 'keywords' => 'audit bilan energetique'],
                    ['label_fr' => 'Équipements sous pression', 'label_en' => 'Pressure equipment', 'keywords' => 'pression esv recipient'],
                    ['label_fr' => 'Hydrogène & stockage énergie', 'label_en' => 'Hydrogen & energy storage', 'keywords' => 'hydrogene stockage batterie'],
                ],
            ],
            [
                'slug' => 'energy-electrical-engineer',
                'name_fr' => 'Ingénieur électricien / réseaux électriques',
                'name_en' => 'Electrical / Power Systems Engineer',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur réseaux HTA / HTB', 'label_en' => 'MV / HV network engineer', 'keywords' => 'hta htb reseaux'],
                    ['label_fr' => 'Ingénieur protection / automatismes', 'label_en' => 'Protection / automation engineer', 'keywords' => 'protection automatismes'],
                    ['label_fr' => 'Ingénieur postes électriques', 'label_en' => 'Substation engineer', 'keywords' => 'postes transformateurs'],
                    ['label_fr' => 'Ingénieur smart grid / comptage', 'label_en' => 'Smart grid / metering engineer', 'keywords' => 'smart grid comptage'],
                    ['label_fr' => 'Études électriques BT / TGBT', 'label_en' => 'LV / switchboard studies', 'keywords' => 'bt tgbt etudes'],
                ],
            ],
            [
                'slug' => 'energy-process-engineer',
                'name_fr' => 'Ingénieur process / procédés énergétiques',
                'name_en' => 'Process / Energy Process Engineer',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur process pétrole / gaz', 'label_en' => 'Oil & gas process engineer', 'keywords' => 'process oil gas'],
                    ['label_fr' => 'Ingénieur procédés chimiques / raffinage', 'label_en' => 'Chemical / refining process engineer', 'keywords' => 'raffinage procedes'],
                    ['label_fr' => 'Ingénieur process thermique / cogénération', 'label_en' => 'Thermal / CHP process engineer', 'keywords' => 'cogenration thermique'],
                    ['label_fr' => 'Bilan matière / énergie', 'label_en' => 'Mass / energy balance', 'keywords' => 'bilan matiere energie'],
                ],
            ],
            [
                'slug' => 'energy-civil-structural',
                'name_fr' => 'Ingénieur génie civil / structures énergie',
                'name_en' => 'Civil / Structural Engineer (Energy)',
                'suggestions' => [
                    ['label_fr' => 'Génie civil centrales / sites', 'label_en' => 'Power plant / site civil works', 'keywords' => 'genie civil centrale'],
                    ['label_fr' => 'Fondations éoliennes / solaire', 'label_en' => 'Wind / solar foundations', 'keywords' => 'fondations eolien solaire'],
                    ['label_fr' => 'Structures métalliques / charpente', 'label_en' => 'Steel structures', 'keywords' => 'structures metalliques'],
                    ['label_fr' => 'Pipelines / ouvrages enterrés', 'label_en' => 'Pipelines / buried works', 'keywords' => 'pipeline ouvrages'],
                ],
            ],

            // ——— EnR, nucléaire, hydrogène ———
            [
                'slug' => 'energy-solar-pv',
                'name_fr' => 'Ingénieur / Technicien solaire photovoltaïque',
                'name_en' => 'Solar PV Engineer / Technician',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur conception PV', 'label_en' => 'PV design engineer', 'keywords' => 'photovoltaique conception'],
                    ['label_fr' => 'Chef de chantier solaire', 'label_en' => 'Solar site supervisor', 'keywords' => 'chantier solaire'],
                    ['label_fr' => 'Technicien installation PV', 'label_en' => 'PV installation technician', 'keywords' => 'installation photovoltaique'],
                    ['label_fr' => 'O&M parcs solaires', 'label_en' => 'Solar O&M', 'keywords' => 'om solaire'],
                    ['label_fr' => 'Autoconsommation / toiture / ombrières', 'label_en' => 'Self-consumption / rooftop / carports', 'keywords' => 'autoconsommation toiture'],
                ],
            ],
            [
                'slug' => 'energy-wind',
                'name_fr' => 'Ingénieur / Technicien éolien',
                'name_en' => 'Wind Energy Engineer / Technician',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur développement éolien', 'label_en' => 'Wind development engineer', 'keywords' => 'developpement eolien'],
                    ['label_fr' => 'Technicien maintenance éolienne', 'label_en' => 'Wind turbine technician', 'keywords' => 'maintenance eolienne'],
                    ['label_fr' => 'Ingénieur ressource vent / yield', 'label_en' => 'Wind resource / yield engineer', 'keywords' => 'ressource vent yield'],
                    ['label_fr' => 'Offshore wind / marine', 'label_en' => 'Offshore wind', 'keywords' => 'offshore eolien'],
                ],
            ],
            [
                'slug' => 'energy-hydro-storage',
                'name_fr' => 'Hydroélectricité / Stockage / Batteries',
                'name_en' => 'Hydro / Storage / Batteries',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur hydroélectrique', 'label_en' => 'Hydropower engineer', 'keywords' => 'hydroelectricite barrage'],
                    ['label_fr' => 'Ingénieur stockage batteries / BESS', 'label_en' => 'Battery storage / BESS engineer', 'keywords' => 'bess batteries stockage'],
                    ['label_fr' => 'STEP / pompage turbinage', 'label_en' => 'Pumped storage', 'keywords' => 'step pompage'],
                    ['label_fr' => 'Technicien maintenance hydro', 'label_en' => 'Hydro maintenance technician', 'keywords' => 'maintenance hydro'],
                ],
            ],
            [
                'slug' => 'energy-hydrogen',
                'name_fr' => 'Hydrogène / Power-to-X',
                'name_en' => 'Hydrogen / Power-to-X',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur hydrogène vert', 'label_en' => 'Green hydrogen engineer', 'keywords' => 'hydrogene vert'],
                    ['label_fr' => 'Électrolyse / production H2', 'label_en' => 'Electrolysis / H2 production', 'keywords' => 'electrolyse'],
                    ['label_fr' => 'Stockage & transport hydrogène', 'label_en' => 'H2 storage & transport', 'keywords' => 'stockage transport h2'],
                    ['label_fr' => 'Power-to-X / e-fuels', 'label_en' => 'Power-to-X / e-fuels', 'keywords' => 'power to x efuels'],
                ],
            ],
            [
                'slug' => 'energy-nuclear',
                'name_fr' => 'Ingénieur / Technicien nucléaire',
                'name_en' => 'Nuclear Engineer / Technician',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur sûreté nucléaire', 'label_en' => 'Nuclear safety engineer', 'keywords' => 'surete nucleaire'],
                    ['label_fr' => 'Ingénieur exploitation centrale', 'label_en' => 'Nuclear plant operations engineer', 'keywords' => 'exploitation nucleaire'],
                    ['label_fr' => 'Radioprotection', 'label_en' => 'Radiation protection', 'keywords' => 'radioprotection'],
                    ['label_fr' => 'Maintenance nucléaire', 'label_en' => 'Nuclear maintenance', 'keywords' => 'maintenance nucleaire'],
                    ['label_fr' => 'Démantèlement / déchets radioactifs', 'label_en' => 'Decommissioning / radioactive waste', 'keywords' => 'demantelement dechets'],
                ],
            ],

            // ——— Oil & gas ———
            [
                'slug' => 'energy-oil-gas-engineer',
                'name_fr' => 'Ingénieur pétrole & gaz',
                'name_en' => 'Oil & Gas Engineer',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur réservoir / subsurface', 'label_en' => 'Reservoir / subsurface engineer', 'keywords' => 'reservoir subsurface'],
                    ['label_fr' => 'Ingénieur forage / drilling', 'label_en' => 'Drilling engineer', 'keywords' => 'forage drilling'],
                    ['label_fr' => 'Ingénieur production upstream', 'label_en' => 'Upstream production engineer', 'keywords' => 'upstream production'],
                    ['label_fr' => 'Ingénieur pipelines / midstream', 'label_en' => 'Pipeline / midstream engineer', 'keywords' => 'pipeline midstream'],
                    ['label_fr' => 'Ingénieur raffinage / downstream', 'label_en' => 'Refining / downstream engineer', 'keywords' => 'raffinage downstream'],
                ],
            ],
            [
                'slug' => 'energy-oil-gas-ops',
                'name_fr' => 'Exploitation / Technicien pétrole & gaz',
                'name_en' => 'Oil & Gas Operations / Technician',
                'suggestions' => [
                    ['label_fr' => 'Opérateur de production pétrole / gaz', 'label_en' => 'Oil & gas production operator', 'keywords' => 'operateur production'],
                    ['label_fr' => 'Technicien instrumentation process', 'label_en' => 'Process instrumentation technician', 'keywords' => 'instrumentation'],
                    ['label_fr' => 'Technicien maintenance offshore / onshore', 'label_en' => 'Offshore / onshore maintenance tech', 'keywords' => 'offshore onshore'],
                    ['label_fr' => 'Opérateur raffinerie / terminal', 'label_en' => 'Refinery / terminal operator', 'keywords' => 'raffinerie terminal'],
                ],
            ],

            // ——— Production, réseaux, trading ———
            [
                'slug' => 'energy-power-plant-ops',
                'name_fr' => 'Exploitation centrale / production d\'électricité',
                'name_en' => 'Power Plant Operations',
                'suggestions' => [
                    ['label_fr' => 'Chef d\'équipe exploitation', 'label_en' => 'Operations shift supervisor', 'keywords' => 'chef equipe exploitation'],
                    ['label_fr' => 'Opérateur de centrale thermique / cycle combiné', 'label_en' => 'Thermal / CCGT plant operator', 'keywords' => 'centrale thermique ccgt'],
                    ['label_fr' => 'Conducteur d\'installation énergétique', 'label_en' => 'Energy plant operator', 'keywords' => 'conducteur installation'],
                    ['label_fr' => 'Dispatching / conduite réseau', 'label_en' => 'Dispatching / grid control', 'keywords' => 'dispatching conduite'],
                ],
            ],
            [
                'slug' => 'energy-grid-ops',
                'name_fr' => 'Exploitation / Maintenance réseaux électriques',
                'name_en' => 'Power Grid Operations / Maintenance',
                'suggestions' => [
                    ['label_fr' => 'Technicien maintenance réseaux HTA', 'label_en' => 'MV network maintenance technician', 'keywords' => 'maintenance hta'],
                    ['label_fr' => 'Électricien réseaux / lignes', 'label_en' => 'Line / network electrician', 'keywords' => 'electricien lignes'],
                    ['label_fr' => 'Technicien postes / transformateurs', 'label_en' => 'Substation / transformer technician', 'keywords' => 'postes transformateurs'],
                    ['label_fr' => 'Interventions dépannage / astreinte', 'label_en' => 'Fault response / on-call', 'keywords' => 'depannage astreinte'],
                ],
            ],
            [
                'slug' => 'energy-trading-markets',
                'name_fr' => 'Trading / Marchés de l\'énergie',
                'name_en' => 'Energy Trading / Markets',
                'suggestions' => [
                    ['label_fr' => 'Trader électricité / gaz', 'label_en' => 'Power / gas trader', 'keywords' => 'trader electricite gaz'],
                    ['label_fr' => 'Origination / structuration énergie', 'label_en' => 'Energy origination / structuring', 'keywords' => 'origination'],
                    ['label_fr' => 'Analyste marchés énergie', 'label_en' => 'Energy markets analyst', 'keywords' => 'marches energie'],
                    ['label_fr' => 'Risk manager énergie / PPA', 'label_en' => 'Energy risk / PPA manager', 'keywords' => 'ppa risk energie'],
                    ['label_fr' => 'Scheduling / nomination / balancing', 'label_en' => 'Scheduling / nomination / balancing', 'keywords' => 'scheduling balancing'],
                ],
            ],
            [
                'slug' => 'energy-sales-account',
                'name_fr' => 'Commercial / Chargé d\'affaires énergie',
                'name_en' => 'Energy Sales / Key Account',
                'suggestions' => [
                    ['label_fr' => 'Commercial énergie B2B', 'label_en' => 'B2B energy sales', 'keywords' => 'commercial energie b2b'],
                    ['label_fr' => 'Key Account Manager utilities', 'label_en' => 'Utilities KAM', 'keywords' => 'kam utilities'],
                    ['label_fr' => 'Conseiller énergie particuliers', 'label_en' => 'Residential energy advisor', 'keywords' => 'conseiller energie'],
                    ['label_fr' => 'Business developer EnR / PPA', 'label_en' => 'Renewables / PPA business developer', 'keywords' => 'bizdev enr ppa'],
                    ['label_fr' => 'Ingénieur d\'affaires énergie', 'label_en' => 'Energy sales engineer', 'keywords' => 'ingenieur affaires'],
                ],
            ],

            // ——— Efficacité, bâtiment, fluides ———
            [
                'slug' => 'energy-efficiency-auditor',
                'name_fr' => 'Auditeur / Consultant efficacité énergétique',
                'name_en' => 'Energy Efficiency Auditor / Consultant',
                'suggestions' => [
                    ['label_fr' => 'Auditeur énergétique', 'label_en' => 'Energy auditor', 'keywords' => 'audit energetique'],
                    ['label_fr' => 'Bureau d\'études thermiques', 'label_en' => 'Thermal design office', 'keywords' => 'bet thermique'],
                    ['label_fr' => 'Conseiller rénovation énergétique', 'label_en' => 'Energy retrofit advisor', 'keywords' => 'renovation energetique'],
                    ['label_fr' => 'CEE / certificats d\'économie d\'énergie', 'label_en' => 'Energy savings certificates', 'keywords' => 'cee certificats'],
                    ['label_fr' => 'ISO 50001 / energy management', 'label_en' => 'ISO 50001 / EnMS', 'keywords' => 'iso 50001'],
                ],
            ],
            [
                'slug' => 'hvac-technician',
                'name_fr' => 'Technicien CVC / fluides & réseaux',
                'name_en' => 'HVAC / Fluids & Networks Technician',
                'suggestions' => [
                    ['label_fr' => 'Technicien CVC / climatisation', 'label_en' => 'HVAC / air conditioning technician', 'keywords' => 'cvc climatisation'],
                    ['label_fr' => 'Technicien réseaux électriques', 'label_en' => 'Electrical networks technician', 'keywords' => 'reseaux electriques hta'],
                    ['label_fr' => 'Technicien eau & assainissement', 'label_en' => 'Water & sanitation technician', 'keywords' => 'eau assainissement'],
                    ['label_fr' => 'Technicien froid industriel', 'label_en' => 'Industrial refrigeration technician', 'keywords' => 'froid industriel'],
                    ['label_fr' => 'Technicien chauffage / chaudières', 'label_en' => 'Heating / boiler technician', 'keywords' => 'chauffage chaudiere'],
                ],
            ],
            [
                'slug' => 'energy-maintenance-technician',
                'name_fr' => 'Technicien / Agent de maintenance énergétique',
                'name_en' => 'Energy Maintenance Technician',
                'suggestions' => [
                    ['label_fr' => 'Technicien maintenance industrielle énergie', 'label_en' => 'Industrial energy maintenance tech', 'keywords' => 'maintenance industrielle'],
                    ['label_fr' => 'Électromécanicien', 'label_en' => 'Electromechanic', 'keywords' => 'electromecanicien'],
                    ['label_fr' => 'Technicien automatismes / GTB-GTC', 'label_en' => 'Automation / BMS technician', 'keywords' => 'gtb gtc automatismes'],
                    ['label_fr' => 'Agent polyvalent maintenance site', 'label_en' => 'Multi-skilled site maintenance', 'keywords' => 'polyvalent maintenance'],
                ],
            ],

            // ——— Environnement, eau, déchets ———
            [
                'slug' => 'energy-environment-engineer',
                'name_fr' => 'Ingénieur environnement / HSE environnement',
                'name_en' => 'Environmental / EHS Engineer',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur environnement', 'label_en' => 'Environmental engineer', 'keywords' => 'ingenieur environnement'],
                    ['label_fr' => 'Études d\'impact / ICPE', 'label_en' => 'Impact assessment / ICPE', 'keywords' => 'etude impact icpe'],
                    ['label_fr' => 'Ingénieur sites & sols pollués', 'label_en' => 'Contaminated land engineer', 'keywords' => 'sols pollues ssp'],
                    ['label_fr' => 'Air / émissions / bruit', 'label_en' => 'Air / emissions / noise', 'keywords' => 'emissions air bruit'],
                    ['label_fr' => 'Responsable environnement site', 'label_en' => 'Site environmental manager', 'keywords' => 'responsable environnement'],
                ],
            ],
            [
                'slug' => 'energy-water-wastewater',
                'name_fr' => 'Eau / Assainissement / Traitement',
                'name_en' => 'Water / Wastewater / Treatment',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur eau potable / réseaux', 'label_en' => 'Drinking water / networks engineer', 'keywords' => 'eau potable reseaux'],
                    ['label_fr' => 'Ingénieur assainissement / STEP', 'label_en' => 'Wastewater / WWTP engineer', 'keywords' => 'assainissement step'],
                    ['label_fr' => 'Exploitant station de traitement', 'label_en' => 'Treatment plant operator', 'keywords' => 'exploitation station'],
                    ['label_fr' => 'Technicien qualité de l\'eau', 'label_en' => 'Water quality technician', 'keywords' => 'qualite eau'],
                    ['label_fr' => 'Hydrologue / ressources en eau', 'label_en' => 'Hydrologist / water resources', 'keywords' => 'hydrologie ressources'],
                ],
            ],
            [
                'slug' => 'energy-waste-circular',
                'name_fr' => 'Déchets / Économie circulaire / Recyclage',
                'name_en' => 'Waste / Circular Economy / Recycling',
                'suggestions' => [
                    ['label_fr' => 'Responsable gestion des déchets', 'label_en' => 'Waste management manager', 'keywords' => 'gestion dechets'],
                    ['label_fr' => 'Ingénieur économie circulaire', 'label_en' => 'Circular economy engineer', 'keywords' => 'economie circulaire'],
                    ['label_fr' => 'Exploitant centre de tri / ISDND', 'label_en' => 'Sorting / landfill operator', 'keywords' => 'centre tri isdnd'],
                    ['label_fr' => 'Valorisation énergétique / CSR', 'label_en' => 'Waste-to-energy / RDF', 'keywords' => 'valorisation csr'],
                    ['label_fr' => 'Technicien collecte / logistique déchets', 'label_en' => 'Waste collection / logistics tech', 'keywords' => 'collecte dechets'],
                ],
            ],
            [
                'slug' => 'energy-climate-esg',
                'name_fr' => 'Climat / Carbone / ESG',
                'name_en' => 'Climate / Carbon / ESG',
                'suggestions' => [
                    ['label_fr' => 'Chargé bilan carbone / GHG', 'label_en' => 'Carbon footprint / GHG officer', 'keywords' => 'bilan carbone ghg'],
                    ['label_fr' => 'Consultant climat / adaptation', 'label_en' => 'Climate / adaptation consultant', 'keywords' => 'climat adaptation'],
                    ['label_fr' => 'Responsable ESG / RSE', 'label_en' => 'ESG / CSR manager', 'keywords' => 'esg rse'],
                    ['label_fr' => 'Analyste taxonomie / reporting CSRD', 'label_en' => 'Taxonomy / CSRD reporting analyst', 'keywords' => 'csrd taxonomie'],
                    ['label_fr' => 'Crédits carbone / marchés volontaires', 'label_en' => 'Carbon credits / voluntary markets', 'keywords' => 'credits carbone'],
                ],
            ],
            [
                'slug' => 'energy-biodiversity',
                'name_fr' => 'Biodiversité / Écologie / Espaces naturels',
                'name_en' => 'Biodiversity / Ecology / Natural Areas',
                'suggestions' => [
                    ['label_fr' => 'Écologue / naturaliste', 'label_en' => 'Ecologist / naturalist', 'keywords' => 'ecologue naturaliste'],
                    ['label_fr' => 'Chargé études faune-flore', 'label_en' => 'Flora-fauna study officer', 'keywords' => 'faune flore'],
                    ['label_fr' => 'Gestionnaire d\'espaces naturels', 'label_en' => 'Natural areas manager', 'keywords' => 'espaces naturels'],
                    ['label_fr' => 'Compensation écologique / ERC', 'label_en' => 'Ecological compensation / mitigation', 'keywords' => 'compensation erc'],
                ],
            ],

            // ——— QHSE, qualité, réglementation ———
            [
                'slug' => 'energy-qhse',
                'name_fr' => 'QHSE / Sécurité industrielle énergie',
                'name_en' => 'QHSE / Industrial Safety (Energy)',
                'suggestions' => [
                    ['label_fr' => 'Responsable QHSE site énergie', 'label_en' => 'Energy site QHSE manager', 'keywords' => 'qhse energie'],
                    ['label_fr' => 'Préventionniste / animateur sécurité', 'label_en' => 'Safety officer / preventionist', 'keywords' => 'prevention securite'],
                    ['label_fr' => 'HSE pétrole & gaz', 'label_en' => 'Oil & gas HSE', 'keywords' => 'hse oil gas'],
                    ['label_fr' => 'Analyse des risques / HAZOP', 'label_en' => 'Risk analysis / HAZOP', 'keywords' => 'hazop risques'],
                    ['label_fr' => 'Auditeur ISO 14001 / 45001', 'label_en' => 'ISO 14001 / 45001 auditor', 'keywords' => 'iso 14001 45001'],
                ],
            ],
            [
                'slug' => 'energy-quality-lab',
                'name_fr' => 'Qualité / Laboratoire / Métrologie',
                'name_en' => 'Quality / Laboratory / Metrology',
                'suggestions' => [
                    ['label_fr' => 'Responsable qualité énergie', 'label_en' => 'Energy quality manager', 'keywords' => 'qualite'],
                    ['label_fr' => 'Technicien laboratoire / analyses', 'label_en' => 'Lab / analysis technician', 'keywords' => 'laboratoire analyses'],
                    ['label_fr' => 'Métrologie / instrumentation', 'label_en' => 'Metrology / instrumentation', 'keywords' => 'metrologie'],
                    ['label_fr' => 'Contrôle qualité combustibles / produits', 'label_en' => 'Fuel / product QC', 'keywords' => 'controle qualite'],
                ],
            ],
            [
                'slug' => 'energy-regulatory-permitting',
                'name_fr' => 'Réglementation / Autorisations / ICPE',
                'name_en' => 'Regulatory / Permitting / ICPE',
                'suggestions' => [
                    ['label_fr' => 'Chargé d\'autorisations / permitting', 'label_en' => 'Permitting officer', 'keywords' => 'autorisations permitting'],
                    ['label_fr' => 'Responsable ICPE / conformité', 'label_en' => 'ICPE / compliance manager', 'keywords' => 'icpe conformite'],
                    ['label_fr' => 'Relations autorités / DREAL', 'label_en' => 'Authority relations / DREAL', 'keywords' => 'dreal autorites'],
                    ['label_fr' => 'Droit de l\'environnement / énergie', 'label_en' => 'Environmental / energy law', 'keywords' => 'droit environnement'],
                ],
            ],

            // ——— Support ———
            [
                'slug' => 'energy-procurement-logistics',
                'name_fr' => 'Achats / Logistique / Supply énergie',
                'name_en' => 'Procurement / Logistics / Energy Supply',
                'suggestions' => [
                    ['label_fr' => 'Acheteur énergie / équipements', 'label_en' => 'Energy / equipment buyer', 'keywords' => 'achats energie'],
                    ['label_fr' => 'Acheteur EPC / sous-traitance', 'label_en' => 'EPC / subcontracting buyer', 'keywords' => 'achats epc'],
                    ['label_fr' => 'Logisticien site / base vie', 'label_en' => 'Site / camp logistics', 'keywords' => 'logistique site'],
                    ['label_fr' => 'Gestionnaire stocks / magasinier technique', 'label_en' => 'Stores / technical warehouse', 'keywords' => 'stocks magasin'],
                ],
            ],
            [
                'slug' => 'energy-finance-control',
                'name_fr' => 'Contrôle de gestion / Finance projets énergie',
                'name_en' => 'Controlling / Project Finance (Energy)',
                'suggestions' => [
                    ['label_fr' => 'Contrôleur de gestion énergie', 'label_en' => 'Energy financial controller', 'keywords' => 'controle gestion'],
                    ['label_fr' => 'Analyste project finance EnR', 'label_en' => 'Renewables project finance analyst', 'keywords' => 'project finance'],
                    ['label_fr' => 'Cost controller projet', 'label_en' => 'Project cost controller', 'keywords' => 'cost control'],
                    ['label_fr' => 'Analyste investissement infrastructure', 'label_en' => 'Infrastructure investment analyst', 'keywords' => 'investissement infra'],
                ],
            ],
            [
                'slug' => 'energy-digital-scada',
                'name_fr' => 'Digital / SCADA / Data énergie',
                'name_en' => 'Digital / SCADA / Energy Data',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur SCADA / automates', 'label_en' => 'SCADA / PLC engineer', 'keywords' => 'scada automate'],
                    ['label_fr' => 'Data analyst énergie', 'label_en' => 'Energy data analyst', 'keywords' => 'data energie'],
                    ['label_fr' => 'IoT / smart metering', 'label_en' => 'IoT / smart metering', 'keywords' => 'iot comptage'],
                    ['label_fr' => 'Cybersécurité OT / industrielle', 'label_en' => 'OT / industrial cybersecurity', 'keywords' => 'cybersecurite ot'],
                ],
            ],
            [
                'slug' => 'energy-hr-training',
                'name_fr' => 'RH / Formation énergie & environnement',
                'name_en' => 'HR / Training (Energy & Environment)',
                'suggestions' => [
                    ['label_fr' => 'Responsable RH site industriel', 'label_en' => 'Industrial site HR manager', 'keywords' => 'rh site'],
                    ['label_fr' => 'Formateur HSE / techniques énergie', 'label_en' => 'HSE / energy technical trainer', 'keywords' => 'formateur hse'],
                    ['label_fr' => 'Chargé recrutement technique', 'label_en' => 'Technical recruiter', 'keywords' => 'recrutement technique'],
                    ['label_fr' => 'Gestionnaire formation / habilitations', 'label_en' => 'Training / certifications admin', 'keywords' => 'habilitations formation'],
                ],
            ],
            [
                'slug' => 'energy-admin-support',
                'name_fr' => 'Assistant(e) / Support administratif énergie',
                'name_en' => 'Assistant / Admin Support (Energy)',
                'suggestions' => [
                    ['label_fr' => 'Assistant(e) de projet énergie', 'label_en' => 'Energy project assistant', 'keywords' => 'assistant projet'],
                    ['label_fr' => 'Assistant(e) technique / bureau d\'études', 'label_en' => 'Technical / design office assistant', 'keywords' => 'assistant technique'],
                    ['label_fr' => 'Secrétaire de direction / site', 'label_en' => 'Site / executive assistant', 'keywords' => 'secretaire site'],
                    ['label_fr' => 'Gestionnaire administratif contrats', 'label_en' => 'Contracts admin officer', 'keywords' => 'contrats administratif'],
                ],
            ],
            [
                'slug' => 'energy-field-operator',
                'name_fr' => 'Opérateur / Agent de terrain énergie & environnement',
                'name_en' => 'Field Operator / Agent (Energy & Environment)',
                'suggestions' => [
                    ['label_fr' => 'Agent d\'exploitation réseaux', 'label_en' => 'Network field agent', 'keywords' => 'agent exploitation'],
                    ['label_fr' => 'Releveur / technicien comptage', 'label_en' => 'Meter reader / metering tech', 'keywords' => 'releve comptage'],
                    ['label_fr' => 'Agent environnement / terrain', 'label_en' => 'Environmental field agent', 'keywords' => 'agent environnement'],
                    ['label_fr' => 'Manœuvre / aide terrain chantier énergie', 'label_en' => 'Energy site labourer / helper', 'keywords' => 'manoeuvre chantier'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'telecom',
        'name_fr' => 'Télécoms & réseaux',
        'name_en' => 'Telecoms & networks',
        'professions' => [
            [
                'slug' => 'telecom-operations-director',
                'name_fr' => 'Directeur / Directrice des Opérations Télécoms',
                'name_en' => 'Telecom Operations Director',
                'suggestions' => [
                    ['label_fr' => 'Direction réseau opérateur', 'label_en' => 'Operator network leadership', 'keywords' => 'operations telecom operateur'],
                    ['label_fr' => 'Pilotage performance réseau', 'label_en' => 'Network performance leadership', 'keywords' => 'kpi qos disponibilite'],
                    ['label_fr' => 'Transformation réseau', 'label_en' => 'Network transformation', 'keywords' => 'modernisation 5g fibre'],
                    ['label_fr' => 'Direction technique télécoms', 'label_en' => 'Telecom technical direction', 'keywords' => 'cto technique telecom'],
                ],
            ],
            [
                'slug' => 'telecom-project-manager',
                'name_fr' => 'Chef de Projet Télécoms / Déploiement Réseau',
                'name_en' => 'Telecom / Network Rollout Project Manager',
                'suggestions' => [
                    ['label_fr' => 'Déploiement sites / BTS', 'label_en' => 'Site / BTS rollout', 'keywords' => 'bts site deploiement'],
                    ['label_fr' => 'Déploiement FTTH / fibre', 'label_en' => 'FTTH / fibre rollout', 'keywords' => 'ftth fibre optique'],
                    ['label_fr' => 'Pilotage sous-traitants', 'label_en' => 'Contractor management', 'keywords' => 'sous-traitance deployment'],
                    ['label_fr' => 'Planning & jalons', 'label_en' => 'Planning & milestones', 'keywords' => 'planning jalons projet'],
                ],
            ],
            [
                'slug' => 'telecom-digital-pmo',
                'name_fr' => 'PMO / Chef de Projet Services Digitaux Télécoms',
                'name_en' => 'Telecom Digital Services PMO / Project Manager',
                'suggestions' => [
                    ['label_fr' => 'Mobile Money / paiements', 'label_en' => 'Mobile Money / payments', 'keywords' => 'mobile money paiement wallet'],
                    ['label_fr' => 'Intégrations partenaires', 'label_en' => 'Partner integrations', 'keywords' => 'integration partenaires api'],
                    ['label_fr' => 'SIT / UAT / mise en production', 'label_en' => 'SIT / UAT / go-live', 'keywords' => 'sit uat mise en production'],
                    ['label_fr' => 'Incidents & anomalies', 'label_en' => 'Incidents & defects', 'keywords' => 'anomalies incidents techniques'],
                    ['label_fr' => 'Reporting & réconciliation', 'label_en' => 'Reporting & reconciliation', 'keywords' => 'reporting reconciliation donnees'],
                ],
            ],
            [
                'slug' => 'network-architect-telecom',
                'name_fr' => 'Architecte / Ingénieur Réseau Télécoms',
                'name_en' => 'Telecom Network Architect / Engineer',
                'suggestions' => [
                    ['label_fr' => 'Architecture IP / cœur de réseau', 'label_en' => 'IP / core network architecture', 'keywords' => 'ip core backbone mpls'],
                    ['label_fr' => 'Conception accès / aggregation', 'label_en' => 'Access / aggregation design', 'keywords' => 'access aggregation'],
                    ['label_fr' => 'Dimensionnement capacité', 'label_en' => 'Capacity planning', 'keywords' => 'capacite dimensionnement'],
                    ['label_fr' => 'Migration / modernisation', 'label_en' => 'Migration / modernization', 'keywords' => 'migration upgrade'],
                ],
            ],
            [
                'slug' => 'radio-rf-engineer',
                'name_fr' => 'Ingénieur Radio / RF',
                'name_en' => 'Radio / RF Engineer',
                'suggestions' => [
                    ['label_fr' => 'Planification radio 4G / 5G', 'label_en' => '4G / 5G radio planning', 'keywords' => 'radio planning 4g 5g'],
                    ['label_fr' => 'Optimisation RF', 'label_en' => 'RF optimization', 'keywords' => 'optimisation rf kpi'],
                    ['label_fr' => 'Drive test / mesures', 'label_en' => 'Drive test / measurements', 'keywords' => 'drive test mesures'],
                    ['label_fr' => 'Sites / antennes', 'label_en' => 'Sites / antennas', 'keywords' => 'antenne site radio'],
                ],
            ],
            [
                'slug' => 'transmission-fibre-engineer',
                'name_fr' => 'Ingénieur Transmission / Fibre Optique',
                'name_en' => 'Transmission / Optical Fibre Engineer',
                'suggestions' => [
                    ['label_fr' => 'Transmission DWDM / optique', 'label_en' => 'DWDM / optical transmission', 'keywords' => 'dwdm optique transmission'],
                    ['label_fr' => 'Réseau FTTH / OLT', 'label_en' => 'FTTH / OLT network', 'keywords' => 'ftth olt gpon'],
                    ['label_fr' => 'Backhaul / transport', 'label_en' => 'Backhaul / transport', 'keywords' => 'backhaul transport'],
                    ['label_fr' => 'Ingénierie liaisons', 'label_en' => 'Link engineering', 'keywords' => 'liaison ingenierie'],
                ],
            ],
            [
                'slug' => 'noc-supervisor',
                'name_fr' => 'Responsable NOC / Supervision Réseau',
                'name_en' => 'NOC / Network Operations Supervisor',
                'suggestions' => [
                    ['label_fr' => 'Supervision 24/7', 'label_en' => '24/7 monitoring', 'keywords' => 'noc supervision 24h'],
                    ['label_fr' => 'Gestion d\'incidents', 'label_en' => 'Incident management', 'keywords' => 'incident ticket escalade'],
                    ['label_fr' => 'Disponibilité / SLA', 'label_en' => 'Availability / SLA', 'keywords' => 'sla disponibilite'],
                    ['label_fr' => 'Encadrement opérateurs NOC', 'label_en' => 'NOC operator leadership', 'keywords' => 'equipe noc'],
                ],
            ],
            [
                'slug' => 'telecom-technician',
                'name_fr' => 'Technicien Télécoms / Réseaux',
                'name_en' => 'Telecom / Network Technician',
                'suggestions' => [
                    ['label_fr' => 'Maintenance réseau', 'label_en' => 'Network maintenance', 'keywords' => 'maintenance telecom'],
                    ['label_fr' => 'Intervention terrain', 'label_en' => 'Field intervention', 'keywords' => 'terrain intervention'],
                    ['label_fr' => 'Câblage / brassage', 'label_en' => 'Cabling / patching', 'keywords' => 'cablage brassage'],
                    ['label_fr' => 'Tests & mesures', 'label_en' => 'Testing & measurements', 'keywords' => 'tests mesures'],
                ],
            ],
            [
                'slug' => 'radio-site-technician',
                'name_fr' => 'Technicien Radio / Sites / BTS',
                'name_en' => 'Radio / Site / BTS Technician',
                'suggestions' => [
                    ['label_fr' => 'Installation BTS', 'label_en' => 'BTS installation', 'keywords' => 'bts installation'],
                    ['label_fr' => 'Maintenance sites radio', 'label_en' => 'Radio site maintenance', 'keywords' => 'site radio maintenance'],
                    ['label_fr' => 'Antennes / RF terrain', 'label_en' => 'Antennas / field RF', 'keywords' => 'antenne rf terrain'],
                    ['label_fr' => 'Mise en service', 'label_en' => 'Commissioning', 'keywords' => 'mise en service'],
                ],
            ],
            [
                'slug' => 'fibre-optic-technician',
                'name_fr' => 'Technicien Fibre Optique / FTTH',
                'name_en' => 'Fibre Optic / FTTH Technician',
                'suggestions' => [
                    ['label_fr' => 'Tirage / pose fibre', 'label_en' => 'Fibre pulling / installation', 'keywords' => 'pose fibre tirage'],
                    ['label_fr' => 'Soudure / raccordement', 'label_en' => 'Splicing / termination', 'keywords' => 'soudure raccordement'],
                    ['label_fr' => 'Réflectométrie OTDR', 'label_en' => 'OTDR testing', 'keywords' => 'otdr reflectometrie'],
                    ['label_fr' => 'Raccordement client FTTH', 'label_en' => 'FTTH customer drop', 'keywords' => 'raccordement client ftth'],
                ],
            ],
            [
                'slug' => 'telecom-installer',
                'name_fr' => 'Installateur / Monteur Télécoms',
                'name_en' => 'Telecom Installer / Fitter',
                'suggestions' => [
                    ['label_fr' => 'Installation équipements', 'label_en' => 'Equipment installation', 'keywords' => 'installation equipements'],
                    ['label_fr' => 'Montage pylônes / supports', 'label_en' => 'Tower / support assembly', 'keywords' => 'pylone montage'],
                    ['label_fr' => 'Câblage client / entreprise', 'label_en' => 'Customer / enterprise cabling', 'keywords' => 'cablage client'],
                    ['label_fr' => 'Mise en conformité', 'label_en' => 'Compliance fit-out', 'keywords' => 'conformite securite'],
                ],
            ],
            [
                'slug' => 'telecom-support-technician',
                'name_fr' => 'Technicien Support / Hotline Technique Télécoms',
                'name_en' => 'Telecom Technical Support / Hotline Technician',
                'suggestions' => [
                    ['label_fr' => 'Support N1 / N2', 'label_en' => 'L1 / L2 support', 'keywords' => 'support n1 n2'],
                    ['label_fr' => 'Diagnostic panne', 'label_en' => 'Fault diagnosis', 'keywords' => 'diagnostic panne'],
                    ['label_fr' => 'Hotline entreprise', 'label_en' => 'Enterprise hotline', 'keywords' => 'hotline b2b'],
                    ['label_fr' => 'Ticket / escalade', 'label_en' => 'Ticketing / escalation', 'keywords' => 'ticket escalade'],
                ],
            ],
            [
                'slug' => 'telecom-b2b-sales',
                'name_fr' => 'Commercial B2B Télécoms / Grands Comptes',
                'name_en' => 'Telecom B2B / Key Account Sales',
                'suggestions' => [
                    ['label_fr' => 'Grands comptes entreprises', 'label_en' => 'Enterprise key accounts', 'keywords' => 'grands comptes b2b'],
                    ['label_fr' => 'Solutions connectivité', 'label_en' => 'Connectivity solutions', 'keywords' => 'connectivite vpn sdwan'],
                    ['label_fr' => 'Offres cloud / IoT opérateur', 'label_en' => 'Operator cloud / IoT offers', 'keywords' => 'iot cloud operateur'],
                    ['label_fr' => 'Négociation contrats', 'label_en' => 'Contract negotiation', 'keywords' => 'negociation contrat'],
                ],
            ],
            [
                'slug' => 'telecom-retail-advisor',
                'name_fr' => 'Conseiller(ère) Clientèle / Boutique Opérateur',
                'name_en' => 'Customer Advisor / Operator Store',
                'suggestions' => [
                    ['label_fr' => 'Vente magasin opérateur', 'label_en' => 'Operator store sales', 'keywords' => 'boutique operateur'],
                    ['label_fr' => 'Mobile / forfaits', 'label_en' => 'Mobile / plans', 'keywords' => 'forfait mobile'],
                    ['label_fr' => 'Accueil & SAV client', 'label_en' => 'Customer service & after-sales', 'keywords' => 'sav accueil'],
                    ['label_fr' => 'Upsell / rétention', 'label_en' => 'Upsell / retention', 'keywords' => 'upsell retention'],
                ],
            ],
            [
                'slug' => 'telecom-account-manager',
                'name_fr' => 'Chargé(e) d\'Affaires Entreprises Télécoms',
                'name_en' => 'Telecom Business Account Manager',
                'suggestions' => [
                    ['label_fr' => 'Portefeuille PME / ETI', 'label_en' => 'SME / mid-market portfolio', 'keywords' => 'pme eti portefeuille'],
                    ['label_fr' => 'Fidélisation clients', 'label_en' => 'Customer retention', 'keywords' => 'fidelisation'],
                    ['label_fr' => 'Besoins connectivité', 'label_en' => 'Connectivity needs', 'keywords' => 'besoin connectivite'],
                    ['label_fr' => 'Suivi commercial', 'label_en' => 'Commercial follow-up', 'keywords' => 'suivi commercial'],
                ],
            ],
            [
                'slug' => 'telecom-product-manager',
                'name_fr' => 'Responsable Produit / Offre Télécoms',
                'name_en' => 'Telecom Product / Offer Manager',
                'suggestions' => [
                    ['label_fr' => 'Offre mobile', 'label_en' => 'Mobile offer', 'keywords' => 'offre mobile'],
                    ['label_fr' => 'Offre fixe / internet', 'label_en' => 'Fixed / internet offer', 'keywords' => 'fixe internet ftth'],
                    ['label_fr' => 'Pricing & packaging', 'label_en' => 'Pricing & packaging', 'keywords' => 'pricing packaging'],
                    ['label_fr' => 'Lancement produit', 'label_en' => 'Product launch', 'keywords' => 'lancement produit'],
                ],
            ],
            [
                'slug' => 'oss-bss-analyst',
                'name_fr' => 'Analyste OSS / BSS / Billing Télécoms',
                'name_en' => 'Telecom OSS / BSS / Billing Analyst',
                'suggestions' => [
                    ['label_fr' => 'Billing / facturation', 'label_en' => 'Billing / invoicing', 'keywords' => 'billing facturation'],
                    ['label_fr' => 'OSS / inventaire réseau', 'label_en' => 'OSS / network inventory', 'keywords' => 'oss inventaire'],
                    ['label_fr' => 'BSS / CRM opérateur', 'label_en' => 'BSS / operator CRM', 'keywords' => 'bss crm'],
                    ['label_fr' => 'Intégration SI télécoms', 'label_en' => 'Telecom IT integration', 'keywords' => 'integration si telecom'],
                ],
            ],
            [
                'slug' => 'telecom-regulation-specialist',
                'name_fr' => 'Spécialiste Régulation / Interconnexion',
                'name_en' => 'Regulation / Interconnection Specialist',
                'suggestions' => [
                    ['label_fr' => 'Régulation télécoms', 'label_en' => 'Telecom regulation', 'keywords' => 'regulation anrt'],
                    ['label_fr' => 'Interconnexion opérateurs', 'label_en' => 'Operator interconnection', 'keywords' => 'interconnexion'],
                    ['label_fr' => 'Licences / fréquences', 'label_en' => 'Licences / spectrum', 'keywords' => 'licences frequences'],
                    ['label_fr' => 'Conformité réglementaire', 'label_en' => 'Regulatory compliance', 'keywords' => 'conformite reglementaire'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'finance',
        'name_fr' => 'Banque, finance & assurance',
        'name_en' => 'Banking, finance & insurance',
        'professions' => [
            // ——— Direction & encadrement ———
            [
                'slug' => 'cfo',
                'name_fr' => 'Direction financière / DAF',
                'name_en' => 'Finance leadership / CFO',
                'suggestions' => [
                    ['label_fr' => 'Directeur administratif et financier (DAF)', 'label_en' => 'Chief financial officer (CFO)', 'keywords' => 'daf cfo direction finance'],
                    ['label_fr' => 'Directeur financier', 'label_en' => 'Finance director', 'keywords' => 'directeur financier'],
                    ['label_fr' => 'Directeur financier groupe / holding', 'label_en' => 'Group finance director', 'keywords' => 'groupe consolidation holding'],
                    ['label_fr' => 'Secrétaire général / adjoint direction', 'label_en' => 'General secretary / deputy CEO', 'keywords' => 'secretariat general comex'],
                ],
            ],
            [
                'slug' => 'bank-branch-director',
                'name_fr' => 'Directeur / Responsable d\'agence bancaire',
                'name_en' => 'Bank Branch Manager / Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur d\'agence', 'label_en' => 'Branch director', 'keywords' => 'agence bancaire management'],
                    ['label_fr' => 'Responsable d\'agence', 'label_en' => 'Branch manager', 'keywords' => 'responsable agence reseau'],
                    ['label_fr' => 'Directeur adjoint d\'agence', 'label_en' => 'Deputy branch manager', 'keywords' => 'adjoint agence'],
                    ['label_fr' => 'Directeur de centre d\'affaires', 'label_en' => 'Business center director', 'keywords' => 'centre affaires entreprises'],
                ],
            ],
            [
                'slug' => 'bank-network-director',
                'name_fr' => 'Direction réseau / régionale bancaire',
                'name_en' => 'Banking Network / Regional Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur régional banque', 'label_en' => 'Regional banking director', 'keywords' => 'regional reseau banque'],
                    ['label_fr' => 'Directeur de marché / réseau', 'label_en' => 'Market / network director', 'keywords' => 'reseau commercial banque'],
                    ['label_fr' => 'Inspecteur / animateur réseau', 'label_en' => 'Network inspector / coach', 'keywords' => 'animation reseau inspection'],
                    ['label_fr' => 'Directeur des engagements', 'label_en' => 'Credit approvals director', 'keywords' => 'engagements credit decision'],
                ],
            ],
            [
                'slug' => 'insurance-agency-director',
                'name_fr' => 'Directeur / Responsable d\'agence d\'assurance',
                'name_en' => 'Insurance Agency Manager / Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur d\'agence assurance', 'label_en' => 'Insurance agency director', 'keywords' => 'agence assurance management'],
                    ['label_fr' => 'Responsable commercial assurance', 'label_en' => 'Insurance sales manager', 'keywords' => 'commercial assurance equipe'],
                    ['label_fr' => 'Inspecteur commercial assurance', 'label_en' => 'Insurance sales inspector', 'keywords' => 'inspecteur commercial'],
                    ['label_fr' => 'Directeur de région assurance', 'label_en' => 'Regional insurance director', 'keywords' => 'region assurance'],
                ],
            ],

            // ——— Banque de détail / réseau ———
            [
                'slug' => 'bank-advisor',
                'name_fr' => 'Conseiller clientèle bancaire (particuliers)',
                'name_en' => 'Retail Bank Relationship Manager',
                'suggestions' => [
                    ['label_fr' => 'Conseiller clientèle particuliers', 'label_en' => 'Retail relationship manager', 'keywords' => 'particuliers banque conseil'],
                    ['label_fr' => 'Conseiller bancaire polyvalent', 'label_en' => 'General bank advisor', 'keywords' => 'conseiller polyvalent'],
                    ['label_fr' => 'Conseiller crédit immobilier', 'label_en' => 'Mortgage advisor', 'keywords' => 'credit immobilier pret'],
                    ['label_fr' => 'Conseiller monétique / moyens de paiement', 'label_en' => 'Payments / cards advisor', 'keywords' => 'monetique cartes paiement'],
                    ['label_fr' => 'Conseiller bancassurance', 'label_en' => 'Bancassurance advisor', 'keywords' => 'bancassurance produits'],
                ],
            ],
            [
                'slug' => 'bank-teller-welcome',
                'name_fr' => 'Chargé(e) d\'accueil / Guichetier(ère)',
                'name_en' => 'Bank Teller / Front Desk Officer',
                'suggestions' => [
                    ['label_fr' => 'Chargé(e) d\'accueil bancaire', 'label_en' => 'Bank welcome officer', 'keywords' => 'accueil banque'],
                    ['label_fr' => 'Guichetier(ère) / caissier(ère)', 'label_en' => 'Teller / cashier', 'keywords' => 'guichet caisse operations'],
                    ['label_fr' => 'Agent d\'opérations bancaires', 'label_en' => 'Bank operations clerk', 'keywords' => 'operations guichet'],
                    ['label_fr' => 'Hôte(sse) d\'accueil agence', 'label_en' => 'Branch host / greeter', 'keywords' => 'accueil orientation'],
                ],
            ],
            [
                'slug' => 'bank-teleadvisor',
                'name_fr' => 'Conseiller / Téléconseiller bancaire',
                'name_en' => 'Bank Call Center Advisor',
                'suggestions' => [
                    ['label_fr' => 'Téléconseiller bancaire', 'label_en' => 'Bank teleadvisor', 'keywords' => 'teleconseiller call center'],
                    ['label_fr' => 'Conseiller distanciel / digital', 'label_en' => 'Remote / digital advisor', 'keywords' => 'conseil digital distance'],
                    ['label_fr' => 'Chargé relation clientèle à distance', 'label_en' => 'Remote customer care officer', 'keywords' => 'relation client distance'],
                    ['label_fr' => 'Support clientèle banque en ligne', 'label_en' => 'Online banking support', 'keywords' => 'banque en ligne support'],
                ],
            ],
            [
                'slug' => 'bank-professional-advisor',
                'name_fr' => 'Conseiller / Chargé d\'affaires professionnels',
                'name_en' => 'SME / Professionals Relationship Manager',
                'suggestions' => [
                    ['label_fr' => 'Conseiller clientèle professionnels', 'label_en' => 'Professionals segment advisor', 'keywords' => 'professionnels TPE'],
                    ['label_fr' => 'Chargé d\'affaires TPE / PME', 'label_en' => 'SME relationship manager', 'keywords' => 'tpe pme affaires'],
                    ['label_fr' => 'Conseiller professions libérales', 'label_en' => 'Self-employed professionals advisor', 'keywords' => 'professions liberales'],
                    ['label_fr' => 'Chargé de clientèle artisans / commerçants', 'label_en' => 'Trades & retail clients advisor', 'keywords' => 'artisans commercants'],
                ],
            ],

            // ——— Banque privée / patrimoine ———
            [
                'slug' => 'private-banker',
                'name_fr' => 'Banquier privé / Conseiller en gestion de patrimoine',
                'name_en' => 'Private Banker / Wealth Advisor',
                'suggestions' => [
                    ['label_fr' => 'Banquier privé', 'label_en' => 'Private banker', 'keywords' => 'private banking fortune'],
                    ['label_fr' => 'Conseiller en gestion de patrimoine (CGP)', 'label_en' => 'Wealth management advisor', 'keywords' => 'cgp patrimoine'],
                    ['label_fr' => 'Conseiller patrimonial', 'label_en' => 'Patrimonial advisor', 'keywords' => 'patrimoine fiscalite'],
                    ['label_fr' => 'Family office / ultra high net worth', 'label_en' => 'Family office / UHNW', 'keywords' => 'family office uhnw'],
                    ['label_fr' => 'Ingénieur patrimonial', 'label_en' => 'Wealth engineer', 'keywords' => 'ingenierie patrimoniale'],
                ],
            ],

            // ——— Banque entreprises / corporate / financement ———
            [
                'slug' => 'corporate-relationship-manager',
                'name_fr' => 'Chargé d\'affaires entreprises / Corporate',
                'name_en' => 'Corporate Relationship Manager',
                'suggestions' => [
                    ['label_fr' => 'Chargé d\'affaires entreprises', 'label_en' => 'Corporate RM', 'keywords' => 'entreprises corporate affaires'],
                    ['label_fr' => 'Chargé d\'affaires grands comptes', 'label_en' => 'Large corporates RM', 'keywords' => 'grands comptes'],
                    ['label_fr' => 'Chargé d\'affaires mid-market', 'label_en' => 'Mid-market RM', 'keywords' => 'mid market ETI'],
                    ['label_fr' => 'Banquier d\'affaires junior / senior', 'label_en' => 'Investment banking associate', 'keywords' => 'banque affaires M&A'],
                ],
            ],
            [
                'slug' => 'structured-trade-finance',
                'name_fr' => 'Financement structuré / Trade finance / Immobilier',
                'name_en' => 'Structured / Trade / Real Estate Finance',
                'suggestions' => [
                    ['label_fr' => 'Chargé de financement structuré', 'label_en' => 'Structured finance officer', 'keywords' => 'financement structure'],
                    ['label_fr' => 'Trade finance / crédits documentaires', 'label_en' => 'Trade finance / LCs', 'keywords' => 'trade finance credoc'],
                    ['label_fr' => 'Financement de projets / infrastructures', 'label_en' => 'Project / infrastructure finance', 'keywords' => 'project finance'],
                    ['label_fr' => 'Chargé d\'affaires immobilières', 'label_en' => 'Real estate finance RM', 'keywords' => 'immobilier promotion'],
                    ['label_fr' => 'Financement LBO / acquisition', 'label_en' => 'LBO / acquisition finance', 'keywords' => 'lbo acquisition'],
                ],
            ],
            [
                'slug' => 'credit-analyst-approvals',
                'name_fr' => 'Analyste crédit / Engagements',
                'name_en' => 'Credit Analyst / Credit Approvals',
                'suggestions' => [
                    ['label_fr' => 'Analyste crédit entreprises', 'label_en' => 'Corporate credit analyst', 'keywords' => 'analyste credit entreprises'],
                    ['label_fr' => 'Analyste engagements / décision crédit', 'label_en' => 'Credit decision analyst', 'keywords' => 'engagements decision'],
                    ['label_fr' => 'Analyste scoring / retail credit', 'label_en' => 'Retail credit / scoring analyst', 'keywords' => 'scoring retail'],
                    ['label_fr' => 'Chargé d\'instruction de dossiers crédit', 'label_en' => 'Credit file underwriter', 'keywords' => 'instruction dossier credit'],
                    ['label_fr' => 'Analyste contrepartie / notation interne', 'label_en' => 'Counterparty / internal rating analyst', 'keywords' => 'notation irb contrepartie'],
                ],
            ],
            [
                'slug' => 'collections-recovery',
                'name_fr' => 'Recouvrement / Contentieux / Recovery',
                'name_en' => 'Collections / Recovery / Litigation',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire de recouvrement', 'label_en' => 'Collections officer', 'keywords' => 'recouvrement impayes'],
                    ['label_fr' => 'Chargé recovery / special assets', 'label_en' => 'Recovery / special assets officer', 'keywords' => 'recovery NPL'],
                    ['label_fr' => 'Chargé contentieux bancaire', 'label_en' => 'Banking litigation officer', 'keywords' => 'contentieux banques'],
                    ['label_fr' => 'Gestionnaire amiable / précontentieux', 'label_en' => 'Amicable / pre-litigation officer', 'keywords' => 'amiable precontentieux'],
                ],
            ],

            // ——— Marchés, asset management, investissement ———
            [
                'slug' => 'trader-markets',
                'name_fr' => 'Trader / Sales marchés financiers',
                'name_en' => 'Trader / Markets Sales',
                'suggestions' => [
                    ['label_fr' => 'Trader taux / change / actions', 'label_en' => 'Rates / FX / equity trader', 'keywords' => 'trader marche'],
                    ['label_fr' => 'Sales marchés / flow sales', 'label_en' => 'Markets sales', 'keywords' => 'sales flow marche'],
                    ['label_fr' => 'Structurer produits dérivés', 'label_en' => 'Derivatives structurer', 'keywords' => 'structurer derives'],
                    ['label_fr' => 'Market maker / desk', 'label_en' => 'Market maker / desk', 'keywords' => 'market making desk'],
                ],
            ],
            [
                'slug' => 'portfolio-asset-manager',
                'name_fr' => 'Gestionnaire de portefeuille / Asset manager',
                'name_en' => 'Portfolio / Asset Manager',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire de portefeuille', 'label_en' => 'Portfolio manager', 'keywords' => 'portefeuille gérant'],
                    ['label_fr' => 'Asset manager / gérant OPCVM', 'label_en' => 'Asset / fund manager', 'keywords' => 'asset management opcvm'],
                    ['label_fr' => 'Gérant actions / obligations', 'label_en' => 'Equity / fixed income manager', 'keywords' => 'actions obligations'],
                    ['label_fr' => 'Multi-assets / allocation', 'label_en' => 'Multi-asset / allocation', 'keywords' => 'allocation multi assets'],
                    ['label_fr' => 'Private equity / venture capital', 'label_en' => 'Private equity / VC', 'keywords' => 'private equity venture'],
                ],
            ],
            [
                'slug' => 'financial-markets-analyst',
                'name_fr' => 'Analyste financier / Research / Investissement',
                'name_en' => 'Financial / Research / Investment Analyst',
                'suggestions' => [
                    ['label_fr' => 'Analyste buy-side', 'label_en' => 'Buy-side analyst', 'keywords' => 'buy side recherche'],
                    ['label_fr' => 'Analyste sell-side / equity research', 'label_en' => 'Sell-side / equity research', 'keywords' => 'sell side equity research'],
                    ['label_fr' => 'Analyste crédit marchés', 'label_en' => 'Credit markets analyst', 'keywords' => 'credit research'],
                    ['label_fr' => 'Analyste ESG / investissement responsable', 'label_en' => 'ESG / responsible investment analyst', 'keywords' => 'esg isr'],
                    ['label_fr' => 'Analyste M&A / corporate finance', 'label_en' => 'M&A / corporate finance analyst', 'keywords' => 'm&a corporate finance'],
                ],
            ],
            [
                'slug' => 'broker-intermediary',
                'name_fr' => 'Courtier / Intermédiaire financier',
                'name_en' => 'Broker / Financial Intermediary',
                'suggestions' => [
                    ['label_fr' => 'Courtier en crédits', 'label_en' => 'Credit broker', 'keywords' => 'courtier credit'],
                    ['label_fr' => 'Courtier en assurance', 'label_en' => 'Insurance broker', 'keywords' => 'courtier assurance'],
                    ['label_fr' => 'CIF / conseiller en investissements financiers', 'label_en' => 'Financial investment advisor (CIF)', 'keywords' => 'cif investissements'],
                    ['label_fr' => 'Agent général d\'assurance', 'label_en' => 'Tied insurance agent', 'keywords' => 'agent general assurance'],
                ],
            ],

            // ——— Risques ———
            [
                'slug' => 'credit-risk-analyst',
                'name_fr' => 'Analyste / Manager risque de crédit',
                'name_en' => 'Credit Risk Analyst / Manager',
                'suggestions' => [
                    ['label_fr' => 'Analyste risque crédit', 'label_en' => 'Credit risk analyst', 'keywords' => 'risque credit'],
                    ['label_fr' => 'Risk manager crédit', 'label_en' => 'Credit risk manager', 'keywords' => 'risk manager credit'],
                    ['label_fr' => 'Modélisation risque / IFRS 9 / PD LGD', 'label_en' => 'Credit risk modelling / IFRS 9', 'keywords' => 'ifrs9 pd lgd ead'],
                    ['label_fr' => 'Surveillance portefeuille / early warning', 'label_en' => 'Portfolio monitoring / EWS', 'keywords' => 'surveillance early warning'],
                ],
            ],
            [
                'slug' => 'market-op-risk',
                'name_fr' => 'Risque de marché / opérationnel / liquidité',
                'name_en' => 'Market / Operational / Liquidity Risk',
                'suggestions' => [
                    ['label_fr' => 'Analyste risque de marché', 'label_en' => 'Market risk analyst', 'keywords' => 'risque marche var'],
                    ['label_fr' => 'Analyste risque opérationnel', 'label_en' => 'Operational risk analyst', 'keywords' => 'risque operationnel'],
                    ['label_fr' => 'Risk manager liquidité / ALM', 'label_en' => 'Liquidity / ALM risk manager', 'keywords' => 'liquidite alm'],
                    ['label_fr' => 'Risque modèle / validation modèles', 'label_en' => 'Model risk / validation', 'keywords' => 'model risk validation'],
                ],
            ],
            [
                'slug' => 'enterprise-risk-manager',
                'name_fr' => 'Risk manager / Pilotage des risques',
                'name_en' => 'Enterprise Risk Manager',
                'suggestions' => [
                    ['label_fr' => 'Risk manager entreprise', 'label_en' => 'Enterprise risk manager', 'keywords' => 'erm risk management'],
                    ['label_fr' => 'Directeur des risques (CRO)', 'label_en' => 'Chief risk officer (CRO)', 'keywords' => 'cro direction risques'],
                    ['label_fr' => 'Cartographie / appétit au risque', 'label_en' => 'Risk mapping / appetite', 'keywords' => 'cartographie appetit risque'],
                    ['label_fr' => 'Reporting risques / ICAAP ILAAP', 'label_en' => 'Risk reporting / ICAAP ILAAP', 'keywords' => 'icaap ilaap reporting'],
                ],
            ],

            // ——— Conformité, contrôle, audit ———
            [
                'slug' => 'compliance-officer',
                'name_fr' => 'Compliance officer / Conformité',
                'name_en' => 'Compliance Officer',
                'suggestions' => [
                    ['label_fr' => 'Compliance officer', 'label_en' => 'Compliance officer', 'keywords' => 'compliance conformite'],
                    ['label_fr' => 'RCCI / RCSI', 'label_en' => 'RCCI / RCSI', 'keywords' => 'rcci rcsi controle'],
                    ['label_fr' => 'Conformité réglementaire banque', 'label_en' => 'Banking regulatory compliance', 'keywords' => 'reglementaire banque'],
                    ['label_fr' => 'Conformité assurance / ACPR', 'label_en' => 'Insurance compliance / ACPR', 'keywords' => 'acpr assurance'],
                    ['label_fr' => 'Éthique / conflits d\'intérêts', 'label_en' => 'Ethics / conflicts of interest', 'keywords' => 'ethique conflits'],
                ],
            ],
            [
                'slug' => 'aml-kyc-officer',
                'name_fr' => 'LCB-FT / AML / KYC',
                'name_en' => 'AML / KYC / Financial Crime',
                'suggestions' => [
                    ['label_fr' => 'Analyste LCB-FT / AML', 'label_en' => 'AML / CTF analyst', 'keywords' => 'lcb-ft aml blanchiment'],
                    ['label_fr' => 'Analyste KYC / onboarding', 'label_en' => 'KYC / onboarding analyst', 'keywords' => 'kyc onboarding'],
                    ['label_fr' => 'Investigateur alertes / transaction monitoring', 'label_en' => 'Alert investigator / TM', 'keywords' => 'alertes monitoring'],
                    ['label_fr' => 'Responsable LCB-FT', 'label_en' => 'AML officer / MLRO', 'keywords' => 'responsable lcb-ft mlro'],
                    ['label_fr' => 'Sanctions / embargoes / PEP', 'label_en' => 'Sanctions / embargoes / PEP', 'keywords' => 'sanctions embargos pep'],
                ],
            ],
            [
                'slug' => 'internal-control-audit',
                'name_fr' => 'Contrôle interne / Audit interne',
                'name_en' => 'Internal Control / Internal Audit',
                'suggestions' => [
                    ['label_fr' => 'Contrôleur permanent', 'label_en' => 'Permanent control officer', 'keywords' => 'controle permanent'],
                    ['label_fr' => 'Contrôle périodique / audit interne', 'label_en' => 'Periodic control / internal audit', 'keywords' => 'audit interne periodique'],
                    ['label_fr' => 'Auditeur interne banque / assurance', 'label_en' => 'Banking / insurance internal auditor', 'keywords' => 'auditeur interne'],
                    ['label_fr' => 'Inspecteur général', 'label_en' => 'General inspector', 'keywords' => 'inspection generale'],
                    ['label_fr' => 'Contrôle qualité opérations', 'label_en' => 'Operations quality control', 'keywords' => 'qualite operations'],
                ],
            ],
            [
                'slug' => 'external-audit-statutory',
                'name_fr' => 'Audit externe / Commissariat aux comptes',
                'name_en' => 'External Audit / Statutory Audit',
                'suggestions' => [
                    ['label_fr' => 'Auditeur financier (Big 4 / cabinet)', 'label_en' => 'Financial auditor (Big 4 / firm)', 'keywords' => 'audit externe big4'],
                    ['label_fr' => 'Commissaire aux comptes', 'label_en' => 'Statutory auditor', 'keywords' => 'commissaire comptes'],
                    ['label_fr' => 'Auditeur IT / systèmes', 'label_en' => 'IT systems auditor', 'keywords' => 'audit it systemes'],
                    ['label_fr' => 'Auditeur conformité / réglementaire', 'label_en' => 'Compliance / regulatory auditor', 'keywords' => 'audit conformite'],
                ],
            ],

            // ——— Assurance ———
            [
                'slug' => 'insurance-advisor-sales',
                'name_fr' => 'Conseiller / Commercial assurance',
                'name_en' => 'Insurance Advisor / Sales',
                'suggestions' => [
                    ['label_fr' => 'Conseiller assurance IARD', 'label_en' => 'P&C insurance advisor', 'keywords' => 'iard auto habitation'],
                    ['label_fr' => 'Conseiller prévoyance / santé', 'label_en' => 'Life / health insurance advisor', 'keywords' => 'prevoyance sante'],
                    ['label_fr' => 'Conseiller épargne / assurance-vie', 'label_en' => 'Savings / life insurance advisor', 'keywords' => 'epargne assurance vie'],
                    ['label_fr' => 'Commercial grands risques / entreprises', 'label_en' => 'Commercial lines / corporate risks', 'keywords' => 'grands risques entreprises'],
                    ['label_fr' => 'Téléconseiller assurance', 'label_en' => 'Insurance teleadvisor', 'keywords' => 'teleconseiller assurance'],
                ],
            ],
            [
                'slug' => 'insurance-underwriter',
                'name_fr' => 'Souscripteur / Tarification assurance',
                'name_en' => 'Insurance Underwriter / Pricing',
                'suggestions' => [
                    ['label_fr' => 'Souscripteur IARD', 'label_en' => 'P&C underwriter', 'keywords' => 'souscription iard'],
                    ['label_fr' => 'Souscripteur vie / prévoyance', 'label_en' => 'Life / protection underwriter', 'keywords' => 'souscription vie'],
                    ['label_fr' => 'Souscripteur grands risques', 'label_en' => 'Large risks underwriter', 'keywords' => 'grands risques'],
                    ['label_fr' => 'Tarificateur / pricing assurance', 'label_en' => 'Insurance pricing analyst', 'keywords' => 'tarification pricing'],
                    ['label_fr' => 'Souscripteur réassurance', 'label_en' => 'Reinsurance underwriter', 'keywords' => 'reassurance'],
                ],
            ],
            [
                'slug' => 'claims-manager',
                'name_fr' => 'Gestionnaire / Inspecteur sinistres',
                'name_en' => 'Claims Handler / Claims Inspector',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire sinistres IARD', 'label_en' => 'P&C claims handler', 'keywords' => 'sinistres iard'],
                    ['label_fr' => 'Gestionnaire sinistres corporels', 'label_en' => 'Bodily injury claims handler', 'keywords' => 'sinistres corporels'],
                    ['label_fr' => 'Inspecteur sinistres', 'label_en' => 'Claims inspector', 'keywords' => 'inspecteur sinistres'],
                    ['label_fr' => 'Chargé d\'indemnisation', 'label_en' => 'Indemnity officer', 'keywords' => 'indemnisation'],
                    ['label_fr' => 'Gestionnaire sinistres entreprises', 'label_en' => 'Commercial claims handler', 'keywords' => 'sinistres entreprises'],
                ],
            ],
            [
                'slug' => 'actuary',
                'name_fr' => 'Actuaire / Actuariat',
                'name_en' => 'Actuary / Actuarial',
                'suggestions' => [
                    ['label_fr' => 'Actuaire pricing / tarification', 'label_en' => 'Pricing actuary', 'keywords' => 'actuaire pricing'],
                    ['label_fr' => 'Actuaire provisions / reserving', 'label_en' => 'Reserving actuary', 'keywords' => 'provisions reserving'],
                    ['label_fr' => 'Actuaire Solvabilité II / ORSA', 'label_en' => 'Solvency II / ORSA actuary', 'keywords' => 'solvabilite orsa'],
                    ['label_fr' => 'Actuaire ALM / produits', 'label_en' => 'ALM / product actuary', 'keywords' => 'alm produits'],
                    ['label_fr' => 'Data scientist actuariel', 'label_en' => 'Actuarial data scientist', 'keywords' => 'data actuariat'],
                ],
            ],
            [
                'slug' => 'insurance-operations',
                'name_fr' => 'Gestion / Production assurance',
                'name_en' => 'Insurance Operations / Production',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire de production', 'label_en' => 'Policy production officer', 'keywords' => 'production contrats'],
                    ['label_fr' => 'Gestionnaire de contrats / avenants', 'label_en' => 'Policy / endorsement administrator', 'keywords' => 'contrats avenants'],
                    ['label_fr' => 'Gestionnaire prestations santé / prévoyance', 'label_en' => 'Health / protection benefits officer', 'keywords' => 'prestations sante'],
                    ['label_fr' => 'Back-office assurance', 'label_en' => 'Insurance back office', 'keywords' => 'back office assurance'],
                ],
            ],

            // ——— Comptabilité, contrôle, trésorerie ———
            [
                'slug' => 'accountant',
                'name_fr' => 'Comptable / Comptabilité',
                'name_en' => 'Accountant / Accounting',
                'suggestions' => [
                    ['label_fr' => 'Comptable général', 'label_en' => 'General accountant', 'keywords' => 'comptabilite generale bilan'],
                    ['label_fr' => 'Comptable bancaire / comptabilité banque', 'label_en' => 'Bank accountant', 'keywords' => 'comptabilite bancaire'],
                    ['label_fr' => 'Comptable clients / fournisseurs', 'label_en' => 'AR / AP accountant', 'keywords' => 'clients fournisseurs'],
                    ['label_fr' => 'Comptable consolidation', 'label_en' => 'Consolidation accountant', 'keywords' => 'consolidation ifrs'],
                    ['label_fr' => 'Expert-comptable / collaborateur EC', 'label_en' => 'Chartered accountant / firm associate', 'keywords' => 'expert comptable cabinet'],
                    ['label_fr' => 'Aide-comptable / assistant comptable', 'label_en' => 'Accounting assistant', 'keywords' => 'aide comptable'],
                ],
            ],
            [
                'slug' => 'financial-controller',
                'name_fr' => 'Contrôleur de gestion / FP&A',
                'name_en' => 'Management Controller / FP&A',
                'suggestions' => [
                    ['label_fr' => 'Contrôleur de gestion', 'label_en' => 'Financial / management controller', 'keywords' => 'controle gestion reporting'],
                    ['label_fr' => 'Analyste FP&A', 'label_en' => 'FP&A analyst', 'keywords' => 'fpa budget previsionnel'],
                    ['label_fr' => 'Contrôleur de gestion commercial / réseau', 'label_en' => 'Commercial / network controller', 'keywords' => 'controle commercial reseau'],
                    ['label_fr' => 'Business partner finance', 'label_en' => 'Finance business partner', 'keywords' => 'business partner finance'],
                    ['label_fr' => 'Contrôleur budgétaire', 'label_en' => 'Budget controller', 'keywords' => 'budgetaire'],
                ],
            ],
            [
                'slug' => 'treasury-cash-manager',
                'name_fr' => 'Trésorier / Cash manager',
                'name_en' => 'Treasurer / Cash Manager',
                'suggestions' => [
                    ['label_fr' => 'Trésorier d\'entreprise', 'label_en' => 'Corporate treasurer', 'keywords' => 'tresorerie'],
                    ['label_fr' => 'Cash manager', 'label_en' => 'Cash manager', 'keywords' => 'cash management'],
                    ['label_fr' => 'Trésorier bancaire / desk trésorerie', 'label_en' => 'Bank treasury desk', 'keywords' => 'tresorerie bancaire'],
                    ['label_fr' => 'Gestionnaire de liquidité', 'label_en' => 'Liquidity manager', 'keywords' => 'liquidite'],
                    ['label_fr' => 'Credit manager', 'label_en' => 'Credit manager', 'keywords' => 'credit manager encours'],
                ],
            ],
            [
                'slug' => 'regulatory-reporting-finance',
                'name_fr' => 'Reporting réglementaire / Comptabilité réglementaire',
                'name_en' => 'Regulatory Reporting / Regulatory Accounting',
                'suggestions' => [
                    ['label_fr' => 'Chargé reporting réglementaire banque', 'label_en' => 'Banking regulatory reporting', 'keywords' => 'reporting reglementaire bale'],
                    ['label_fr' => 'Reporting Solvabilité II / prudentiel', 'label_en' => 'Solvency II / prudential reporting', 'keywords' => 'solvabilite prudentiel'],
                    ['label_fr' => 'Analyste Bâle / fonds propres', 'label_en' => 'Basel / capital analyst', 'keywords' => 'bale fonds propres rwa'],
                    ['label_fr' => 'Comptable IFRS / normes bancaires', 'label_en' => 'IFRS / banking GAAP accountant', 'keywords' => 'ifrs normes bancaires'],
                ],
            ],
            [
                'slug' => 'payroll-social-accounting',
                'name_fr' => 'Gestionnaire de paie / Social',
                'name_en' => 'Payroll / Social Accounting',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire de paie', 'label_en' => 'Payroll officer', 'keywords' => 'paie social'],
                    ['label_fr' => 'Responsable paie', 'label_en' => 'Payroll manager', 'keywords' => 'responsable paie'],
                    ['label_fr' => 'Comptable social / charges sociales', 'label_en' => 'Social contributions accountant', 'keywords' => 'charges sociales'],
                    ['label_fr' => 'Administrateur paie & avantages sociaux', 'label_en' => 'Payroll & benefits admin', 'keywords' => 'avantages sociaux'],
                ],
            ],

            // ——— Middle / Back office ———
            [
                'slug' => 'middle-back-office',
                'name_fr' => 'Middle office / Back office',
                'name_en' => 'Middle Office / Back Office',
                'suggestions' => [
                    ['label_fr' => 'Middle office marchés', 'label_en' => 'Markets middle office', 'keywords' => 'middle office'],
                    ['label_fr' => 'Back office titres / dérivés', 'label_en' => 'Securities / derivatives BO', 'keywords' => 'back office titres'],
                    ['label_fr' => 'Back office crédits / engagements', 'label_en' => 'Loans / credit back office', 'keywords' => 'back office credits'],
                    ['label_fr' => 'Opérations / settlements', 'label_en' => 'Operations / settlements', 'keywords' => 'settlements operations'],
                    ['label_fr' => 'Custody / dépositaire', 'label_en' => 'Custody / depositary', 'keywords' => 'custody depositaire'],
                ],
            ],
            [
                'slug' => 'payments-operations',
                'name_fr' => 'Moyens de paiement / Opérations de flux',
                'name_en' => 'Payments / Cash Operations',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire moyens de paiement', 'label_en' => 'Payments operations officer', 'keywords' => 'moyens paiement'],
                    ['label_fr' => 'Opérateur flux / virements SEPA SWIFT', 'label_en' => 'Payments flow / SEPA SWIFT operator', 'keywords' => 'sepa swift virements'],
                    ['label_fr' => 'Fraude paiements / chargeback', 'label_en' => 'Payments fraud / chargeback', 'keywords' => 'fraude paiements'],
                    ['label_fr' => 'Product owner paiements / monétique', 'label_en' => 'Payments / cards product owner', 'keywords' => 'monetique product'],
                ],
            ],

            // ——— Fonctions support ———
            [
                'slug' => 'banking-legal-counsel',
                'name_fr' => 'Juriste banque / assurance / finance',
                'name_en' => 'Banking / Insurance Legal Counsel',
                'suggestions' => [
                    ['label_fr' => 'Juriste bancaire', 'label_en' => 'Banking counsel', 'keywords' => 'juriste bancaire'],
                    ['label_fr' => 'Juriste assurance', 'label_en' => 'Insurance counsel', 'keywords' => 'juriste assurance'],
                    ['label_fr' => 'Juriste marchés / produits financiers', 'label_en' => 'Markets / products counsel', 'keywords' => 'juriste marches'],
                    ['label_fr' => 'Juriste contentieux / recovery', 'label_en' => 'Litigation / recovery counsel', 'keywords' => 'contentieux'],
                    ['label_fr' => 'Data protection officer (DPO)', 'label_en' => 'Data protection officer (DPO)', 'keywords' => 'dpo rgpd'],
                ],
            ],
            [
                'slug' => 'banking-product-marketing',
                'name_fr' => 'Marketing / Produits bancaires & assurance',
                'name_en' => 'Banking & Insurance Product / Marketing',
                'suggestions' => [
                    ['label_fr' => 'Product manager bancaire', 'label_en' => 'Banking product manager', 'keywords' => 'product manager banque'],
                    ['label_fr' => 'Chef de produit assurance', 'label_en' => 'Insurance product manager', 'keywords' => 'chef produit assurance'],
                    ['label_fr' => 'Chargé marketing bancaire', 'label_en' => 'Bank marketing officer', 'keywords' => 'marketing bancaire'],
                    ['label_fr' => 'CRM / customer journey banque', 'label_en' => 'Bank CRM / customer journey', 'keywords' => 'crm parcours client'],
                    ['label_fr' => 'Chargé expérience client', 'label_en' => 'Customer experience officer', 'keywords' => 'experience client nps'],
                ],
            ],
            [
                'slug' => 'banking-data-bi',
                'name_fr' => 'Data / BI / Quant finance',
                'name_en' => 'Data / BI / Quant Finance',
                'suggestions' => [
                    ['label_fr' => 'Data analyst banque / assurance', 'label_en' => 'Banking / insurance data analyst', 'keywords' => 'data analyst finance'],
                    ['label_fr' => 'Business intelligence / reporting', 'label_en' => 'BI / reporting analyst', 'keywords' => 'bi powerbi'],
                    ['label_fr' => 'Quant / ingénieur financier', 'label_en' => 'Quant / financial engineer', 'keywords' => 'quant ingenierie financiere'],
                    ['label_fr' => 'Data scientist risque / fraude', 'label_en' => 'Risk / fraud data scientist', 'keywords' => 'data scientist fraude'],
                ],
            ],
            [
                'slug' => 'banking-it-project',
                'name_fr' => 'Chef de projet SI / Transformation digitale finance',
                'name_en' => 'Banking IT Project / Digital Transformation',
                'suggestions' => [
                    ['label_fr' => 'Chef de projet SI bancaire', 'label_en' => 'Banking IT project manager', 'keywords' => 'projet si bancaire'],
                    ['label_fr' => 'Business analyst banque / assurance', 'label_en' => 'Banking / insurance BA', 'keywords' => 'business analyst'],
                    ['label_fr' => 'MOA / AMOA finance', 'label_en' => 'Business owner / BA finance', 'keywords' => 'moa amoa'],
                    ['label_fr' => 'Transformation digitale / change', 'label_en' => 'Digital transformation / change', 'keywords' => 'transformation digitale'],
                    ['label_fr' => 'Consultant core banking / assurance', 'label_en' => 'Core banking / insurance consultant', 'keywords' => 'core banking'],
                ],
            ],
            [
                'slug' => 'banking-hr-training',
                'name_fr' => 'RH / Formation banque & assurance',
                'name_en' => 'HR / Learning (Banking & Insurance)',
                'suggestions' => [
                    ['label_fr' => 'Responsable RH réseau bancaire', 'label_en' => 'Banking network HR manager', 'keywords' => 'rh banque'],
                    ['label_fr' => 'Chargé recrutement finance', 'label_en' => 'Finance recruiter', 'keywords' => 'recrutement finance'],
                    ['label_fr' => 'Responsable formation / academy', 'label_en' => 'L&D / academy manager', 'keywords' => 'formation academy'],
                    ['label_fr' => 'Gestionnaire RH / administration du personnel', 'label_en' => 'HR admin / people ops', 'keywords' => 'administration personnel'],
                ],
            ],
            [
                'slug' => 'banking-support-assistant',
                'name_fr' => 'Assistant(e) / Support administratif finance',
                'name_en' => 'Assistant / Admin Support (Finance)',
                'suggestions' => [
                    ['label_fr' => 'Assistant(e) commercial(e) banque', 'label_en' => 'Bank sales assistant', 'keywords' => 'assistant commercial'],
                    ['label_fr' => 'Assistant(e) back-office', 'label_en' => 'Back-office assistant', 'keywords' => 'assistant back office'],
                    ['label_fr' => 'Secrétaire de direction / assistant direction', 'label_en' => 'Executive assistant', 'keywords' => 'secretaire direction'],
                    ['label_fr' => 'Gestionnaire administratif agence', 'label_en' => 'Branch admin officer', 'keywords' => 'administratif agence'],
                    ['label_fr' => 'Chargé support agence / middle office commercial', 'label_en' => 'Branch / commercial MO support', 'keywords' => 'support agence'],
                ],
            ],
            [
                'slug' => 'banking-procurement-facilities',
                'name_fr' => 'Achats / Moyens généraux / Sécurité',
                'name_en' => 'Procurement / Facilities / Security',
                'suggestions' => [
                    ['label_fr' => 'Acheteur banque / assurance', 'label_en' => 'Banking / insurance buyer', 'keywords' => 'achats'],
                    ['label_fr' => 'Responsable moyens généraux', 'label_en' => 'Facilities manager', 'keywords' => 'moyens generaux'],
                    ['label_fr' => 'Responsable sécurité des biens / personnes', 'label_en' => 'Physical security manager', 'keywords' => 'securite agences'],
                    ['label_fr' => 'Chargé logistique / archives', 'label_en' => 'Logistics / archives officer', 'keywords' => 'archives logistique'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'commerce',
        'name_fr' => 'Commerce & distribution',
        'name_en' => 'Retail & distribution',
        'professions' => [
            // ——— Direction & encadrement ———
            [
                'slug' => 'retail-director',
                'name_fr' => 'Directeur / Directrice retail / magasin',
                'name_en' => 'Retail / Store Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur de magasin / hypermarché', 'label_en' => 'Store / hypermarket director', 'keywords' => 'directeur magasin hypermarche'],
                    ['label_fr' => 'Directeur régional retail', 'label_en' => 'Regional retail director', 'keywords' => 'directeur regional retail'],
                    ['label_fr' => 'Directeur d\'enseigne / réseau', 'label_en' => 'Brand / network director', 'keywords' => 'enseigne reseau'],
                    ['label_fr' => 'Directeur des opérations retail', 'label_en' => 'Retail operations director', 'keywords' => 'operations retail'],
                ],
            ],
            [
                'slug' => 'retail-manager',
                'name_fr' => 'Responsable de magasin / Point de vente',
                'name_en' => 'Store Manager / Point of Sale Manager',
                'suggestions' => [
                    ['label_fr' => 'Responsable de magasin', 'label_en' => 'Store manager', 'keywords' => 'responsable magasin retail'],
                    ['label_fr' => 'Adjoint(e) responsable de magasin', 'label_en' => 'Assistant store manager', 'keywords' => 'adjoint magasin'],
                    ['label_fr' => 'Responsable de boutique / franchise', 'label_en' => 'Boutique / franchise manager', 'keywords' => 'boutique franchise'],
                    ['label_fr' => 'Store manager e-retail / click & collect', 'label_en' => 'E-retail / click & collect manager', 'keywords' => 'click collect'],
                ],
            ],
            [
                'slug' => 'retail-department-manager',
                'name_fr' => 'Chef de rayon / Responsable de secteur',
                'name_en' => 'Department / Section Manager',
                'suggestions' => [
                    ['label_fr' => 'Chef de rayon alimentaire', 'label_en' => 'Food department manager', 'keywords' => 'chef rayon alimentaire'],
                    ['label_fr' => 'Chef de rayon non alimentaire', 'label_en' => 'Non-food department manager', 'keywords' => 'rayon non alimentaire'],
                    ['label_fr' => 'Responsable frais / boucherie / poissonnerie', 'label_en' => 'Fresh / butcher / fishmonger lead', 'keywords' => 'frais boucherie'],
                    ['label_fr' => 'Responsable textile / mode', 'label_en' => 'Apparel / fashion lead', 'keywords' => 'textile mode'],
                    ['label_fr' => 'Responsable électroménager / high-tech', 'label_en' => 'Appliances / high-tech lead', 'keywords' => 'electromenager hightech'],
                ],
            ],

            // ——— Force de vente B2B / terrain ———
            [
                'slug' => 'sales-rep',
                'name_fr' => 'Commercial / Attaché(e) commercial(e)',
                'name_en' => 'Sales Representative',
                'suggestions' => [
                    ['label_fr' => 'Commercial B2B', 'label_en' => 'B2B sales', 'keywords' => 'prospection grand compte'],
                    ['label_fr' => 'Commercial itinérant / terrain', 'label_en' => 'Field sales rep', 'keywords' => 'commercial terrain itinerant'],
                    ['label_fr' => 'Attaché(e) commercial(e) sédentaire', 'label_en' => 'Inside sales rep', 'keywords' => 'sedentaire inside sales'],
                    ['label_fr' => 'Commercial grands comptes', 'label_en' => 'Key accounts sales', 'keywords' => 'grands comptes'],
                    ['label_fr' => 'Business developer', 'label_en' => 'Business developer', 'keywords' => 'bizdev partenariats'],
                ],
            ],
            [
                'slug' => 'commerce-key-account',
                'name_fr' => 'Key Account Manager / Grands comptes distribution',
                'name_en' => 'Key Account Manager (Distribution)',
                'suggestions' => [
                    ['label_fr' => 'Key Account Manager', 'label_en' => 'Key Account Manager', 'keywords' => 'kam compte cle'],
                    ['label_fr' => 'Responsable grands comptes GMS', 'label_en' => 'Modern trade key accounts', 'keywords' => 'gms grande distribution'],
                    ['label_fr' => 'Responsable comptes enseignes', 'label_en' => 'Retail banner accounts', 'keywords' => 'enseignes comptes'],
                    ['label_fr' => 'Account manager distributeurs', 'label_en' => 'Distributor account manager', 'keywords' => 'distributeurs'],
                ],
            ],
            [
                'slug' => 'commerce-sales-director',
                'name_fr' => 'Directeur commercial / Responsable des ventes',
                'name_en' => 'Sales Director / Sales Manager',
                'suggestions' => [
                    ['label_fr' => 'Directeur commercial', 'label_en' => 'Sales director', 'keywords' => 'directeur commercial'],
                    ['label_fr' => 'Responsable des ventes', 'label_en' => 'Sales manager', 'keywords' => 'responsable ventes'],
                    ['label_fr' => 'Directeur des ventes terrain', 'label_en' => 'Field sales director', 'keywords' => 'ventes terrain'],
                    ['label_fr' => 'Responsable force de vente', 'label_en' => 'Sales force manager', 'keywords' => 'force de vente'],
                ],
            ],
            [
                'slug' => 'commerce-export',
                'name_fr' => 'Commercial export / International',
                'name_en' => 'Export / International Sales',
                'suggestions' => [
                    ['label_fr' => 'Commercial export', 'label_en' => 'Export sales', 'keywords' => 'export international'],
                    ['label_fr' => 'Responsable zone export', 'label_en' => 'Export zone manager', 'keywords' => 'zone export'],
                    ['label_fr' => 'Chargé d\'affaires internationales', 'label_en' => 'International business officer', 'keywords' => 'affaires internationales'],
                    ['label_fr' => 'Export area manager', 'label_en' => 'Export area manager', 'keywords' => 'area manager'],
                ],
            ],
            [
                'slug' => 'commerce-teleprospector',
                'name_fr' => 'Téléprospecteur / Téléconseiller commercial',
                'name_en' => 'Telesales / Teleprospecting',
                'suggestions' => [
                    ['label_fr' => 'Téléprospecteur(trice)', 'label_en' => 'Teleprospector', 'keywords' => 'teleprospection'],
                    ['label_fr' => 'Téléconseiller commercial', 'label_en' => 'Sales teleadvisor', 'keywords' => 'teleconseiller commercial'],
                    ['label_fr' => 'Inside sales / SDR', 'label_en' => 'Inside sales / SDR', 'keywords' => 'sdr inside sales'],
                    ['label_fr' => 'Chargé de prises de rendez-vous', 'label_en' => 'Appointment setter', 'keywords' => 'prise rendez vous'],
                ],
            ],

            // ——— Vente magasin / frontline ———
            [
                'slug' => 'retail-salesperson',
                'name_fr' => 'Vendeur(se) / Conseiller(ère) de vente',
                'name_en' => 'Sales Associate / Retail Advisor',
                'suggestions' => [
                    ['label_fr' => 'Vendeur(se) magasin', 'label_en' => 'Store salesperson', 'keywords' => 'vendeur magasin'],
                    ['label_fr' => 'Conseiller(ère) de vente', 'label_en' => 'Sales advisor', 'keywords' => 'conseiller vente'],
                    ['label_fr' => 'Vendeur(se) mode / textile', 'label_en' => 'Fashion / apparel salesperson', 'keywords' => 'vendeur mode'],
                    ['label_fr' => 'Vendeur(se) high-tech / électroménager', 'label_en' => 'High-tech / appliances salesperson', 'keywords' => 'vendeur hightech'],
                    ['label_fr' => 'Conseiller(ère) beauté / parfumerie', 'label_en' => 'Beauty / fragrance advisor', 'keywords' => 'beaute parfumerie'],
                ],
            ],
            [
                'slug' => 'retail-cashier',
                'name_fr' => 'Caissier(ère) / Hôtesse de caisse',
                'name_en' => 'Cashier / Checkout Attendant',
                'suggestions' => [
                    ['label_fr' => 'Caissier(ère)', 'label_en' => 'Cashier', 'keywords' => 'caisse encaissement'],
                    ['label_fr' => 'Hôte(sse) de caisse', 'label_en' => 'Checkout host', 'keywords' => 'hotesse caisse'],
                    ['label_fr' => 'Responsable de caisse / encaisseurs', 'label_en' => 'Cash desk supervisor', 'keywords' => 'responsable caisse'],
                    ['label_fr' => 'Self-checkout / caisse automatique', 'label_en' => 'Self-checkout attendant', 'keywords' => 'self checkout'],
                ],
            ],
            [
                'slug' => 'retail-customer-service',
                'name_fr' => 'Service client / SAV / Accueil magasin',
                'name_en' => 'Customer Service / After-Sales / Store Welcome',
                'suggestions' => [
                    ['label_fr' => 'Chargé(e) d\'accueil magasin', 'label_en' => 'Store greeter', 'keywords' => 'accueil magasin'],
                    ['label_fr' => 'Service client / réclamations', 'label_en' => 'Customer service / complaints', 'keywords' => 'service client'],
                    ['label_fr' => 'SAV / technicien après-vente', 'label_en' => 'After-sales / service tech', 'keywords' => 'sav apres vente'],
                    ['label_fr' => 'Hotline / relation client', 'label_en' => 'Hotline / customer care', 'keywords' => 'hotline relation client'],
                ],
            ],
            [
                'slug' => 'retail-security-loss',
                'name_fr' => 'Sûreté / Prévention des pertes',
                'name_en' => 'Security / Loss Prevention',
                'suggestions' => [
                    ['label_fr' => 'Agent de sûreté magasin', 'label_en' => 'Store security officer', 'keywords' => 'surete magasin'],
                    ['label_fr' => 'Responsable prévention des pertes', 'label_en' => 'Loss prevention manager', 'keywords' => 'prevention pertes'],
                    ['label_fr' => 'Vidéosurveillance / contrôle accès', 'label_en' => 'CCTV / access control', 'keywords' => 'videosurveillance'],
                    ['label_fr' => 'Enquêteur fraude / inventaires', 'label_en' => 'Fraud / inventory investigator', 'keywords' => 'fraude inventaire'],
                ],
            ],

            // ——— Métiers spécialisés rayon ———
            [
                'slug' => 'retail-butcher-fish',
                'name_fr' => 'Boucher / Poissonnier / Charcutier',
                'name_en' => 'Butcher / Fishmonger / Deli',
                'suggestions' => [
                    ['label_fr' => 'Boucher(ère)', 'label_en' => 'Butcher', 'keywords' => 'boucherie'],
                    ['label_fr' => 'Poissonnier(ère)', 'label_en' => 'Fishmonger', 'keywords' => 'poissonnerie'],
                    ['label_fr' => 'Charcutier(ère) / traiteur', 'label_en' => 'Charcutier / deli', 'keywords' => 'charcuterie traiteur'],
                    ['label_fr' => 'Responsable atelier découpe', 'label_en' => 'Cutting room supervisor', 'keywords' => 'decoupe atelier'],
                ],
            ],
            [
                'slug' => 'retail-bakery-pastry',
                'name_fr' => 'Boulanger / Pâtissier / Boulangerie rayon',
                'name_en' => 'Baker / Pastry / In-store Bakery',
                'suggestions' => [
                    ['label_fr' => 'Boulanger(ère)', 'label_en' => 'Baker', 'keywords' => 'boulangerie'],
                    ['label_fr' => 'Pâtissier(ère)', 'label_en' => 'Pastry cook', 'keywords' => 'patisserie'],
                    ['label_fr' => 'Vendeur(se) boulangerie-pâtisserie', 'label_en' => 'Bakery-pastry salesperson', 'keywords' => 'vendeur boulangerie'],
                    ['label_fr' => 'Responsable terminal de cuisson', 'label_en' => 'Bake-off terminal supervisor', 'keywords' => 'terminal cuisson'],
                ],
            ],
            [
                'slug' => 'retail-produce-dairy',
                'name_fr' => 'Fruits & légumes / Crèmerie / Épicerie',
                'name_en' => 'Produce / Dairy / Grocery',
                'suggestions' => [
                    ['label_fr' => 'Employé(e) fruits & légumes', 'label_en' => 'Produce clerk', 'keywords' => 'fruits legumes'],
                    ['label_fr' => 'Employé(e) crèmerie / fromagerie', 'label_en' => 'Dairy / cheese clerk', 'keywords' => 'cremerie fromage'],
                    ['label_fr' => 'Employé(e) épicerie / dry grocery', 'label_en' => 'Grocery clerk', 'keywords' => 'epicerie'],
                    ['label_fr' => 'Employé(e) surgelés / traiteur LS', 'label_en' => 'Frozen / ready meals clerk', 'keywords' => 'surgeles traiteur'],
                ],
            ],

            // ——— Merchandising, catégorie, achats ———
            [
                'slug' => 'commerce-category-manager',
                'name_fr' => 'Category manager / Chef de produit distribution',
                'name_en' => 'Category Manager / Retail Product Lead',
                'suggestions' => [
                    ['label_fr' => 'Category manager', 'label_en' => 'Category manager', 'keywords' => 'category assortiment'],
                    ['label_fr' => 'Chef de produit retail', 'label_en' => 'Retail product manager', 'keywords' => 'chef produit retail'],
                    ['label_fr' => 'Responsable assortiment', 'label_en' => 'Assortment manager', 'keywords' => 'assortiment'],
                    ['label_fr' => 'Buyer category / acheteur catégorie', 'label_en' => 'Category buyer', 'keywords' => 'acheteur categorie'],
                ],
            ],
            [
                'slug' => 'commerce-merchandiser',
                'name_fr' => 'Merchandiser / Visual merchandiser',
                'name_en' => 'Merchandiser / Visual Merchandiser',
                'suggestions' => [
                    ['label_fr' => 'Merchandiser terrain', 'label_en' => 'Field merchandiser', 'keywords' => 'merchandising terrain'],
                    ['label_fr' => 'Visual merchandiser', 'label_en' => 'Visual merchandiser', 'keywords' => 'visual merchandising'],
                    ['label_fr' => 'Responsable implantation / planogramme', 'label_en' => 'Planogram / layout manager', 'keywords' => 'planogramme implantation'],
                    ['label_fr' => 'Animateur(trice) commercial(e) / démonstrateur', 'label_en' => 'Brand promoter / demonstrator', 'keywords' => 'animation demonstration'],
                ],
            ],
            [
                'slug' => 'commerce-trade-marketing',
                'name_fr' => 'Trade marketing / Activation enseignes',
                'name_en' => 'Trade Marketing / Shopper Activation',
                'suggestions' => [
                    ['label_fr' => 'Trade marketing manager', 'label_en' => 'Trade marketing manager', 'keywords' => 'trade marketing'],
                    ['label_fr' => 'Shopper marketing', 'label_en' => 'Shopper marketing', 'keywords' => 'shopper marketing'],
                    ['label_fr' => 'Responsable promotions / opérations commerciales', 'label_en' => 'Promotions / trade ops manager', 'keywords' => 'promotions operations'],
                    ['label_fr' => 'Category development / CDM', 'label_en' => 'Category development manager', 'keywords' => 'category development'],
                ],
            ],
            [
                'slug' => 'commerce-buyer',
                'name_fr' => 'Acheteur / Approvisionneur distribution',
                'name_en' => 'Buyer / Retail Procurement',
                'suggestions' => [
                    ['label_fr' => 'Acheteur distribution / retail', 'label_en' => 'Retail buyer', 'keywords' => 'acheteur retail'],
                    ['label_fr' => 'Acheteur international / sourcing', 'label_en' => 'International buyer / sourcing', 'keywords' => 'sourcing international'],
                    ['label_fr' => 'Approvisionneur / planner', 'label_en' => 'Replenishment planner', 'keywords' => 'approvisionnement planner'],
                    ['label_fr' => 'Acheteur MDD / marque propre', 'label_en' => 'Private label buyer', 'keywords' => 'mdd marque propre'],
                ],
            ],

            // ——— E-commerce & omnicanal ———
            [
                'slug' => 'ecommerce',
                'name_fr' => 'Responsable / Manager e-commerce',
                'name_en' => 'E-commerce Manager',
                'suggestions' => [
                    ['label_fr' => 'Responsable e-commerce', 'label_en' => 'E-commerce manager', 'keywords' => 'ecommerce shopify'],
                    ['label_fr' => 'Manager marketplace', 'label_en' => 'Marketplace manager', 'keywords' => 'marketplace amazon'],
                    ['label_fr' => 'Responsable omnicanal / digital retail', 'label_en' => 'Omnichannel / digital retail manager', 'keywords' => 'omnicanal digital retail'],
                    ['label_fr' => 'Product owner e-commerce', 'label_en' => 'E-commerce product owner', 'keywords' => 'product owner ecommerce'],
                ],
            ],
            [
                'slug' => 'commerce-ecommerce-ops',
                'name_fr' => 'Opérations e-commerce / Fulfilment',
                'name_en' => 'E-commerce Operations / Fulfilment',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire commandes e-commerce', 'label_en' => 'E-commerce order officer', 'keywords' => 'commandes ecommerce'],
                    ['label_fr' => 'Responsable fulfilment / picking', 'label_en' => 'Fulfilment / picking manager', 'keywords' => 'fulfilment picking'],
                    ['label_fr' => 'Gestionnaire retours / SAV digital', 'label_en' => 'Returns / digital after-sales', 'keywords' => 'retours sav'],
                    ['label_fr' => 'Content / catalogue produit web', 'label_en' => 'Web product content / catalog', 'keywords' => 'catalogue produit web'],
                ],
            ],
            [
                'slug' => 'commerce-digital-performance',
                'name_fr' => 'Acquisition / Performance digitale retail',
                'name_en' => 'Retail Digital Acquisition / Performance',
                'suggestions' => [
                    ['label_fr' => 'Traffic manager / SEA-SEO retail', 'label_en' => 'Traffic manager / SEA-SEO', 'keywords' => 'traffic manager sea seo'],
                    ['label_fr' => 'CRM / emailing retail', 'label_en' => 'Retail CRM / email', 'keywords' => 'crm emailing'],
                    ['label_fr' => 'Social commerce / community', 'label_en' => 'Social commerce / community', 'keywords' => 'social commerce'],
                    ['label_fr' => 'Analyste web analytics retail', 'label_en' => 'Retail web analytics', 'keywords' => 'web analytics'],
                ],
            ],

            // ——— Supply, entrepôt, logistique magasin ———
            [
                'slug' => 'commerce-warehouse',
                'name_fr' => 'Entrepôt / Préparation de commandes',
                'name_en' => 'Warehouse / Order Picking',
                'suggestions' => [
                    ['label_fr' => 'Préparateur(trice) de commandes', 'label_en' => 'Order picker', 'keywords' => 'preparation commandes'],
                    ['label_fr' => 'Cariste / magasinier(ère)', 'label_en' => 'Forklift / warehouse clerk', 'keywords' => 'cariste magasinier'],
                    ['label_fr' => 'Responsable d\'entrepôt / depot', 'label_en' => 'Warehouse / depot manager', 'keywords' => 'responsable entrepot'],
                    ['label_fr' => 'Réceptionnaire / contrôleur réception', 'label_en' => 'Receiving clerk / checker', 'keywords' => 'reception marchandises'],
                ],
            ],
            [
                'slug' => 'commerce-supply-flow',
                'name_fr' => 'Supply chain / Flux distribution',
                'name_en' => 'Supply Chain / Distribution Flow',
                'suggestions' => [
                    ['label_fr' => 'Responsable supply chain retail', 'label_en' => 'Retail supply chain manager', 'keywords' => 'supply chain retail'],
                    ['label_fr' => 'Planificateur flux / demand planner', 'label_en' => 'Flow / demand planner', 'keywords' => 'demand planning'],
                    ['label_fr' => 'Responsable transport / livraison magasin', 'label_en' => 'Store delivery / transport manager', 'keywords' => 'transport livraison'],
                    ['label_fr' => 'Gestionnaire stocks / inventaires', 'label_en' => 'Inventory / stock controller', 'keywords' => 'stocks inventaires'],
                ],
            ],
            [
                'slug' => 'retail-stock-clerk',
                'name_fr' => 'Employé(e) libre-service / Mise en rayon',
                'name_en' => 'Shelf Stocker / Sales Floor Associate',
                'suggestions' => [
                    ['label_fr' => 'Employé(e) libre-service (ELS)', 'label_en' => 'Sales floor associate', 'keywords' => 'employe libre service'],
                    ['label_fr' => 'Mise en rayon / facing', 'label_en' => 'Shelf stocking / facing', 'keywords' => 'mise en rayon'],
                    ['label_fr' => 'Réassort / réapprovisionnement', 'label_en' => 'Replenishment clerk', 'keywords' => 'reassort'],
                    ['label_fr' => 'Inventoriste', 'label_en' => 'Inventory clerk', 'keywords' => 'inventoriste'],
                ],
            ],
            [
                'slug' => 'commerce-driver-delivery',
                'name_fr' => 'Livreur / Chauffeur distribution',
                'name_en' => 'Delivery Driver / Distribution Driver',
                'suggestions' => [
                    ['label_fr' => 'Livreur(se) à domicile', 'label_en' => 'Home delivery driver', 'keywords' => 'livraison domicile'],
                    ['label_fr' => 'Chauffeur-livreur PL / VL', 'label_en' => 'Truck / van delivery driver', 'keywords' => 'chauffeur livreur'],
                    ['label_fr' => 'Chauffeur entrepôt-magasin', 'label_en' => 'Depot-to-store driver', 'keywords' => 'navette magasin'],
                    ['label_fr' => 'Dispatcher / coordinateur tournées', 'label_en' => 'Dispatcher / route coordinator', 'keywords' => 'dispatch tournees'],
                ],
            ],

            // ——— Franchise, expansion, BtoB distribution ———
            [
                'slug' => 'commerce-franchise-expansion',
                'name_fr' => 'Franchise / Expansion / Développement réseau',
                'name_en' => 'Franchise / Network Expansion',
                'suggestions' => [
                    ['label_fr' => 'Responsable développement franchise', 'label_en' => 'Franchise development manager', 'keywords' => 'franchise developpement'],
                    ['label_fr' => 'Animateur réseau franchise', 'label_en' => 'Franchise network coach', 'keywords' => 'animation reseau'],
                    ['label_fr' => 'Responsable expansion / immobilier commercial', 'label_en' => 'Expansion / retail real estate', 'keywords' => 'expansion immobilier'],
                    ['label_fr' => 'Franchisee support / relation franchisés', 'label_en' => 'Franchisee support', 'keywords' => 'support franchises'],
                ],
            ],
            [
                'slug' => 'commerce-wholesale',
                'name_fr' => 'Grossiste / Distribution B2B / Cash & carry',
                'name_en' => 'Wholesale / B2B Distribution / Cash & Carry',
                'suggestions' => [
                    ['label_fr' => 'Commercial grossiste', 'label_en' => 'Wholesale sales', 'keywords' => 'grossiste'],
                    ['label_fr' => 'Responsable cash & carry', 'label_en' => 'Cash & carry manager', 'keywords' => 'cash carry'],
                    ['label_fr' => 'Chargé de clientèle professionnels', 'label_en' => 'Trade customer officer', 'keywords' => 'clientele professionnels'],
                    ['label_fr' => 'Distributeur / agent commercial', 'label_en' => 'Distributor / sales agent', 'keywords' => 'agent commercial'],
                ],
            ],

            // ——— Support ———
            [
                'slug' => 'commerce-pricing-promo',
                'name_fr' => 'Pricing / Promotions / Yield retail',
                'name_en' => 'Pricing / Promotions / Retail Yield',
                'suggestions' => [
                    ['label_fr' => 'Analyste pricing', 'label_en' => 'Pricing analyst', 'keywords' => 'pricing'],
                    ['label_fr' => 'Responsable promotions / prospectus', 'label_en' => 'Promotions / flyer manager', 'keywords' => 'promotions prospectus'],
                    ['label_fr' => 'Yield / revenue management retail', 'label_en' => 'Retail yield / revenue', 'keywords' => 'yield revenue'],
                    ['label_fr' => 'Contrôleur de gestion retail', 'label_en' => 'Retail controller', 'keywords' => 'controle gestion retail'],
                ],
            ],
            [
                'slug' => 'commerce-quality-compliance',
                'name_fr' => 'Qualité / Conformité / Hygiène magasin',
                'name_en' => 'Quality / Compliance / Store Hygiene',
                'suggestions' => [
                    ['label_fr' => 'Responsable qualité distribution', 'label_en' => 'Retail quality manager', 'keywords' => 'qualite distribution'],
                    ['label_fr' => 'Hygiène & sécurité alimentaire (HACCP)', 'label_en' => 'Food hygiene / HACCP', 'keywords' => 'haccp hygiene'],
                    ['label_fr' => 'Contrôleur conformité produits', 'label_en' => 'Product compliance officer', 'keywords' => 'conformite produits'],
                    ['label_fr' => 'Auditeur magasin / standards enseigne', 'label_en' => 'Store standards auditor', 'keywords' => 'audit magasin'],
                ],
            ],
            [
                'slug' => 'commerce-data-bi',
                'name_fr' => 'Data / BI / Insights retail',
                'name_en' => 'Retail Data / BI / Insights',
                'suggestions' => [
                    ['label_fr' => 'Data analyst retail', 'label_en' => 'Retail data analyst', 'keywords' => 'data retail'],
                    ['label_fr' => 'Business intelligence / reporting ventes', 'label_en' => 'Sales BI / reporting', 'keywords' => 'bi reporting ventes'],
                    ['label_fr' => 'Customer insights / panel', 'label_en' => 'Customer insights / panel', 'keywords' => 'insights panel'],
                    ['label_fr' => 'Analyste stocks & sell-out', 'label_en' => 'Stock & sell-out analyst', 'keywords' => 'sell out stocks'],
                ],
            ],
            [
                'slug' => 'commerce-hr-training',
                'name_fr' => 'RH / Formation retail',
                'name_en' => 'Retail HR / Training',
                'suggestions' => [
                    ['label_fr' => 'Responsable RH magasin / région', 'label_en' => 'Store / regional HR', 'keywords' => 'rh magasin'],
                    ['label_fr' => 'Formateur(trice) vente / retail academy', 'label_en' => 'Sales / retail academy trainer', 'keywords' => 'formateur vente'],
                    ['label_fr' => 'Chargé recrutement retail', 'label_en' => 'Retail recruiter', 'keywords' => 'recrutement retail'],
                    ['label_fr' => 'Gestionnaire planning / temps de travail', 'label_en' => 'Workforce / schedule planner', 'keywords' => 'planning magasin'],
                ],
            ],
            [
                'slug' => 'commerce-admin-support',
                'name_fr' => 'Assistant(e) / Support administratif commerce',
                'name_en' => 'Commerce Admin / Support Assistant',
                'suggestions' => [
                    ['label_fr' => 'Assistant(e) commercial(e)', 'label_en' => 'Sales assistant (admin)', 'keywords' => 'assistant commercial'],
                    ['label_fr' => 'Assistant(e) de magasin / administratif', 'label_en' => 'Store admin assistant', 'keywords' => 'assistant magasin'],
                    ['label_fr' => 'Gestionnaire administratif commandes', 'label_en' => 'Order admin officer', 'keywords' => 'admin commandes'],
                    ['label_fr' => 'Secrétaire commerciale / ADV', 'label_en' => 'Sales admin / order desk', 'keywords' => 'adv secretariat'],
                ],
            ],
            [
                'slug' => 'commerce-it-pos',
                'name_fr' => 'SI retail / Caisse / Digital en magasin',
                'name_en' => 'Retail IT / POS / In-store Digital',
                'suggestions' => [
                    ['label_fr' => 'Technicien caisse / POS', 'label_en' => 'POS / checkout technician', 'keywords' => 'caisse pos'],
                    ['label_fr' => 'Support SI magasin', 'label_en' => 'Store IT support', 'keywords' => 'si magasin'],
                    ['label_fr' => 'Chef de projet digital in-store', 'label_en' => 'In-store digital project manager', 'keywords' => 'digital instore'],
                    ['label_fr' => 'Administrateur données articles / PIM', 'label_en' => 'Product data / PIM admin', 'keywords' => 'pim articles'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'hospitality',
        'name_fr' => 'Hôtellerie, tourisme & restauration',
        'name_en' => 'Hospitality, tourism & catering',
        'professions' => [
            // ——— Direction & encadrement ———
            [
                'slug' => 'hotel-general-manager',
                'name_fr' => 'Directeur / Directrice d\'hôtel',
                'name_en' => 'Hotel General Manager / Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur d\'hôtel / General Manager', 'label_en' => 'Hotel general manager', 'keywords' => 'directeur hotel gm'],
                    ['label_fr' => 'Directeur adjoint d\'hôtel', 'label_en' => 'Hotel deputy manager', 'keywords' => 'adjoint hotel'],
                    ['label_fr' => 'Directeur d\'exploitation hôtelière', 'label_en' => 'Hotel operations director', 'keywords' => 'exploitation hoteliere'],
                    ['label_fr' => 'Resident manager', 'label_en' => 'Resident manager', 'keywords' => 'resident manager'],
                ],
            ],
            [
                'slug' => 'restaurant-director',
                'name_fr' => 'Directeur / Responsable de restaurant',
                'name_en' => 'Restaurant Director / Manager',
                'suggestions' => [
                    ['label_fr' => 'Directeur de restaurant', 'label_en' => 'Restaurant director', 'keywords' => 'directeur restaurant'],
                    ['label_fr' => 'Responsable de restaurant / gérant', 'label_en' => 'Restaurant manager', 'keywords' => 'gerant restaurant'],
                    ['label_fr' => 'Directeur de restauration collective', 'label_en' => 'Contract catering director', 'keywords' => 'restauration collective'],
                    ['label_fr' => 'Responsable multi-sites / franchise', 'label_en' => 'Multi-site / franchise manager', 'keywords' => 'multi sites franchise'],
                ],
            ],
            [
                'slug' => 'hospitality-fnb-director',
                'name_fr' => 'Directeur Food & Beverage / Restauration hôtelière',
                'name_en' => 'Food & Beverage Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur Food & Beverage (F&B)', 'label_en' => 'F&B director', 'keywords' => 'food beverage fnb'],
                    ['label_fr' => 'Responsable F&B', 'label_en' => 'F&B manager', 'keywords' => 'responsable fnb'],
                    ['label_fr' => 'Directeur de restauration d\'hôtel', 'label_en' => 'Hotel catering director', 'keywords' => 'restauration hotel'],
                    ['label_fr' => 'Responsable banquets / événements', 'label_en' => 'Banquets / events manager', 'keywords' => 'banquets evenements'],
                ],
            ],

            // ——— Cuisine / production ———
            [
                'slug' => 'chef',
                'name_fr' => 'Chef de cuisine / Brigade de cuisine',
                'name_en' => 'Head Chef / Kitchen Brigade',
                'suggestions' => [
                    ['label_fr' => 'Chef de cuisine / Executive chef', 'label_en' => 'Head / executive chef', 'keywords' => 'chef cuisine'],
                    ['label_fr' => 'Sous-chef / Second de cuisine', 'label_en' => 'Sous-chef', 'keywords' => 'sous chef second'],
                    ['label_fr' => 'Chef de partie', 'label_en' => 'Station chef', 'keywords' => 'chef de partie brigade'],
                    ['label_fr' => 'Chef tournant', 'label_en' => 'Relief / tournant chef', 'keywords' => 'chef tournant'],
                    ['label_fr' => 'Commis de cuisine', 'label_en' => 'Commis chef', 'keywords' => 'commis cuisine'],
                ],
            ],
            [
                'slug' => 'hospitality-pastry-bakery',
                'name_fr' => 'Pâtissier / Boulanger / Glacier',
                'name_en' => 'Pastry / Bakery / Ice Cream Chef',
                'suggestions' => [
                    ['label_fr' => 'Pâtissier(ère) / Chef pâtissier', 'label_en' => 'Pastry chef', 'keywords' => 'patisserie'],
                    ['label_fr' => 'Boulanger(ère)', 'label_en' => 'Baker', 'keywords' => 'boulangerie'],
                    ['label_fr' => 'Glacier(ère) / chocolatier(ère)', 'label_en' => 'Ice cream / chocolate maker', 'keywords' => 'glacier chocolatier'],
                    ['label_fr' => 'Chef boulanger-pâtissier hôtel', 'label_en' => 'Hotel bakery-pastry chef', 'keywords' => 'boulanger hotel'],
                ],
            ],
            [
                'slug' => 'hospitality-kitchen-specialist',
                'name_fr' => 'Cuisinier spécialisé / Traiteur',
                'name_en' => 'Specialized Cook / Caterer',
                'suggestions' => [
                    ['label_fr' => 'Cuisinier(ère)', 'label_en' => 'Cook', 'keywords' => 'cuisinier'],
                    ['label_fr' => 'Cuisinier(ère) collective / cantine', 'label_en' => 'Institutional / canteen cook', 'keywords' => 'cuisine collective'],
                    ['label_fr' => 'Traiteur / chef événementiel', 'label_en' => 'Caterer / event chef', 'keywords' => 'traiteur evenementiel'],
                    ['label_fr' => 'Poissonnier / rôtisseur / saucier', 'label_en' => 'Fish / roast / sauce cook', 'keywords' => 'poissonnier rotisseur saucier'],
                    ['label_fr' => 'Plongeur / agent de plonge', 'label_en' => 'Dishwasher / pot wash', 'keywords' => 'plonge'],
                ],
            ],

            // ——— Salle / service restauration ———
            [
                'slug' => 'hospitality-maitre-hotel',
                'name_fr' => 'Maître d\'hôtel / Chef de salle',
                'name_en' => 'Maître d\'Hôtel / Restaurant Floor Manager',
                'suggestions' => [
                    ['label_fr' => 'Maître d\'hôtel', 'label_en' => 'Maître d\'hôtel', 'keywords' => 'maitre hotel'],
                    ['label_fr' => 'Chef de salle / responsable de salle', 'label_en' => 'Floor / dining room manager', 'keywords' => 'chef de salle'],
                    ['label_fr' => 'Chef de rang', 'label_en' => 'Head waiter / section lead', 'keywords' => 'chef de rang'],
                    ['label_fr' => 'Second de salle', 'label_en' => 'Assistant floor manager', 'keywords' => 'second salle'],
                ],
            ],
            [
                'slug' => 'hospitality-waiter',
                'name_fr' => 'Serveur / Commis de salle',
                'name_en' => 'Waiter / Dining Room Attendant',
                'suggestions' => [
                    ['label_fr' => 'Serveur(se) / garçon de café', 'label_en' => 'Waiter / waitress', 'keywords' => 'serveur salle'],
                    ['label_fr' => 'Commis de salle / runner', 'label_en' => 'Busser / runner', 'keywords' => 'commis salle runner'],
                    ['label_fr' => 'Serveur(se) banquet / événementiel', 'label_en' => 'Banquet waiter', 'keywords' => 'serveur banquet'],
                    ['label_fr' => 'Room service / étages restauration', 'label_en' => 'Room service attendant', 'keywords' => 'room service'],
                ],
            ],
            [
                'slug' => 'hospitality-bartender-sommelier',
                'name_fr' => 'Barman / Sommelier / Caviste',
                'name_en' => 'Bartender / Sommelier / Wine Steward',
                'suggestions' => [
                    ['label_fr' => 'Barman / bartender', 'label_en' => 'Bartender', 'keywords' => 'barman bartender'],
                    ['label_fr' => 'Chef barman / responsable bar', 'label_en' => 'Head bartender / bar manager', 'keywords' => 'chef barman'],
                    ['label_fr' => 'Sommelier(ère)', 'label_en' => 'Sommelier', 'keywords' => 'sommelier vins'],
                    ['label_fr' => 'Caviste / conseiller vins', 'label_en' => 'Wine steward / caviste', 'keywords' => 'caviste'],
                ],
            ],
            [
                'slug' => 'hospitality-barista-cafe',
                'name_fr' => 'Barista / Café / Fast casual',
                'name_en' => 'Barista / Café / Fast Casual',
                'suggestions' => [
                    ['label_fr' => 'Barista', 'label_en' => 'Barista', 'keywords' => 'barista cafe'],
                    ['label_fr' => 'Employé(e) restauration rapide', 'label_en' => 'Quick-service restaurant staff', 'keywords' => 'restauration rapide qsr'],
                    ['label_fr' => 'Équipier(ère) cuisine / comptoir', 'label_en' => 'Counter / kitchen crew', 'keywords' => 'equipier comptoir'],
                    ['label_fr' => 'Responsable shift / shift leader', 'label_en' => 'Shift leader', 'keywords' => 'shift leader'],
                ],
            ],

            // ——— Front office / hébergement ———
            [
                'slug' => 'hotel-staff',
                'name_fr' => 'Réception / Front office hôtel',
                'name_en' => 'Hotel Reception / Front Office',
                'suggestions' => [
                    ['label_fr' => 'Réceptionniste hôtel', 'label_en' => 'Hotel receptionist', 'keywords' => 'receptionniste hotel'],
                    ['label_fr' => 'Chef de réception / Front office manager', 'label_en' => 'Front office manager', 'keywords' => 'chef reception'],
                    ['label_fr' => 'Night auditor / réception de nuit', 'label_en' => 'Night auditor', 'keywords' => 'night auditor'],
                    ['label_fr' => 'Guest relation / guest experience', 'label_en' => 'Guest relations', 'keywords' => 'guest relation'],
                    ['label_fr' => 'Agent de réservations', 'label_en' => 'Reservations agent', 'keywords' => 'reservations'],
                ],
            ],
            [
                'slug' => 'hospitality-concierge',
                'name_fr' => 'Concierge / Bagagiste / Voiturier',
                'name_en' => 'Concierge / Bellboy / Valet',
                'suggestions' => [
                    ['label_fr' => 'Concierge / chef concierge', 'label_en' => 'Concierge / head concierge', 'keywords' => 'concierge'],
                    ['label_fr' => 'Bagagiste / bellboy', 'label_en' => 'Bellboy / porter', 'keywords' => 'bagagiste bellboy'],
                    ['label_fr' => 'Voiturier / voiturier parking', 'label_en' => 'Valet parking', 'keywords' => 'voiturier'],
                    ['label_fr' => 'Groom / page', 'label_en' => 'Page / groom', 'keywords' => 'groom page'],
                ],
            ],
            [
                'slug' => 'hospitality-housekeeping',
                'name_fr' => 'Gouvernante / Femme de chambre / Housekeeping',
                'name_en' => 'Housekeeping / Floor Supervisor',
                'suggestions' => [
                    ['label_fr' => 'Gouvernant(e) d\'étages', 'label_en' => 'Executive / floor housekeeper', 'keywords' => 'gouvernante etages'],
                    ['label_fr' => 'Femme / valet de chambre', 'label_en' => 'Room attendant', 'keywords' => 'femme de chambre'],
                    ['label_fr' => 'Equipier(ère) d\'étages', 'label_en' => 'Floor attendant', 'keywords' => 'equipier etages'],
                    ['label_fr' => 'Lingère / blanchisserie', 'label_en' => 'Linen / laundry attendant', 'keywords' => 'lingerie blanchisserie'],
                    ['label_fr' => 'Public area / agent d\'entretien', 'label_en' => 'Public area cleaner', 'keywords' => 'public area entretien'],
                ],
            ],
            [
                'slug' => 'hospitality-spa-wellness',
                'name_fr' => 'Spa / Wellness / Loisirs hôtel',
                'name_en' => 'Spa / Wellness / Hotel Leisure',
                'suggestions' => [
                    ['label_fr' => 'Responsable spa / wellness', 'label_en' => 'Spa / wellness manager', 'keywords' => 'spa wellness'],
                    ['label_fr' => 'Praticien(ne) spa / esthéticien(ne)', 'label_en' => 'Spa therapist / beautician', 'keywords' => 'praticien spa'],
                    ['label_fr' => 'Maître-nageur / animateur piscine', 'label_en' => 'Lifeguard / pool attendant', 'keywords' => 'maitre nageur piscine'],
                    ['label_fr' => 'Animateur(trice) loisirs / kids club', 'label_en' => 'Leisure / kids club animator', 'keywords' => 'animation kids club'],
                ],
            ],
            [
                'slug' => 'hospitality-maintenance',
                'name_fr' => 'Maintenance technique hôtel / Technique',
                'name_en' => 'Hotel Technical Maintenance',
                'suggestions' => [
                    ['label_fr' => 'Responsable technique hôtel', 'label_en' => 'Hotel chief engineer', 'keywords' => 'responsable technique'],
                    ['label_fr' => 'Technicien de maintenance polyvalent', 'label_en' => 'Multi-skilled maintenance tech', 'keywords' => 'maintenance hotel'],
                    ['label_fr' => 'Électricien / plombier hôtel', 'label_en' => 'Hotel electrician / plumber', 'keywords' => 'electricien plombier'],
                    ['label_fr' => 'Technicien CVC / froid hôtel', 'label_en' => 'HVAC / refrigeration tech', 'keywords' => 'cvc froid'],
                ],
            ],

            // ——— Revenue, commercial, events ———
            [
                'slug' => 'hospitality-revenue-manager',
                'name_fr' => 'Revenue manager / Yield hôtelier',
                'name_en' => 'Revenue / Yield Manager',
                'suggestions' => [
                    ['label_fr' => 'Revenue manager', 'label_en' => 'Revenue manager', 'keywords' => 'revenue yield'],
                    ['label_fr' => 'Yield manager / pricing', 'label_en' => 'Yield / pricing manager', 'keywords' => 'yield pricing'],
                    ['label_fr' => 'Analyste revenue / distribution', 'label_en' => 'Revenue / distribution analyst', 'keywords' => 'analyste revenue'],
                    ['label_fr' => 'Channel manager / OTA', 'label_en' => 'Channel / OTA manager', 'keywords' => 'channel manager ota'],
                ],
            ],
            [
                'slug' => 'hospitality-sales-mice',
                'name_fr' => 'Commercial hôtelier / MICE / Banquets',
                'name_en' => 'Hotel Sales / MICE / Banquets',
                'suggestions' => [
                    ['label_fr' => 'Commercial hôtelier', 'label_en' => 'Hotel sales executive', 'keywords' => 'commercial hotelier'],
                    ['label_fr' => 'Responsable MICE / groupes', 'label_en' => 'MICE / groups manager', 'keywords' => 'mice groupes'],
                    ['label_fr' => 'Wedding / événementiel planner', 'label_en' => 'Wedding / event planner', 'keywords' => 'wedding evenementiel'],
                    ['label_fr' => 'Chargé de banquets / salles', 'label_en' => 'Banquet / meeting rooms officer', 'keywords' => 'banquets salles'],
                ],
            ],
            [
                'slug' => 'hospitality-marketing',
                'name_fr' => 'Marketing / Communication hôtellerie-tourisme',
                'name_en' => 'Hospitality & Tourism Marketing',
                'suggestions' => [
                    ['label_fr' => 'Responsable marketing hôtel', 'label_en' => 'Hotel marketing manager', 'keywords' => 'marketing hotel'],
                    ['label_fr' => 'Community manager tourisme', 'label_en' => 'Tourism community manager', 'keywords' => 'community tourisme'],
                    ['label_fr' => 'Chargé e-réputation / avis clients', 'label_en' => 'E-reputation / reviews officer', 'keywords' => 'e-reputation avis'],
                    ['label_fr' => 'Responsable digital / site & OTA', 'label_en' => 'Digital / website & OTA lead', 'keywords' => 'digital ota'],
                ],
            ],

            // ——— Tourisme ———
            [
                'slug' => 'tourism',
                'name_fr' => 'Conseiller voyages / Agent de voyage',
                'name_en' => 'Travel Advisor / Travel Agent',
                'suggestions' => [
                    ['label_fr' => 'Conseiller voyages', 'label_en' => 'Travel advisor', 'keywords' => 'conseiller voyages'],
                    ['label_fr' => 'Agent de voyage / counter', 'label_en' => 'Travel agent / counter', 'keywords' => 'agent voyage'],
                    ['label_fr' => 'Conseiller voyages d\'affaires / MICE', 'label_en' => 'Business travel / MICE advisor', 'keywords' => 'voyages affaires'],
                    ['label_fr' => 'Producteur / forfaitiste tours', 'label_en' => 'Tour package producer', 'keywords' => 'forfaitiste tours'],
                    ['label_fr' => 'Ticketing / billetterie aérienne', 'label_en' => 'Airline ticketing', 'keywords' => 'ticketing billetterie'],
                ],
            ],
            [
                'slug' => 'tourism-guide',
                'name_fr' => 'Guide touristique / Accompagnateur',
                'name_en' => 'Tour Guide / Tour Escort',
                'suggestions' => [
                    ['label_fr' => 'Guide touristique / guide conférencier', 'label_en' => 'Tour / lecture guide', 'keywords' => 'guide touristique'],
                    ['label_fr' => 'Accompagnateur(trice) de voyages', 'label_en' => 'Tour escort', 'keywords' => 'accompagnateur voyages'],
                    ['label_fr' => 'Guide de montagne / nature', 'label_en' => 'Mountain / nature guide', 'keywords' => 'guide montagne'],
                    ['label_fr' => 'Guide culturel / musée / patrimoine', 'label_en' => 'Cultural / museum guide', 'keywords' => 'guide culturel'],
                ],
            ],
            [
                'slug' => 'tourism-operations',
                'name_fr' => 'Exploitation tourisme / TO / Incoming',
                'name_en' => 'Tour Operations / Incoming',
                'suggestions' => [
                    ['label_fr' => 'Responsable opérations TO', 'label_en' => 'Tour operator ops manager', 'keywords' => 'operations to'],
                    ['label_fr' => 'Incoming / DMC ground handling', 'label_en' => 'Incoming / DMC', 'keywords' => 'incoming dmc'],
                    ['label_fr' => 'Chargé de production circuits', 'label_en' => 'Tour production officer', 'keywords' => 'production circuits'],
                    ['label_fr' => 'Responsable réceptif / transfers', 'label_en' => 'Receptive / transfers manager', 'keywords' => 'receptif transfers'],
                ],
            ],
            [
                'slug' => 'tourism-destination',
                'name_fr' => 'Office de tourisme / Destination / Animation',
                'name_en' => 'Tourist Office / Destination / Animation',
                'suggestions' => [
                    ['label_fr' => 'Conseiller office de tourisme', 'label_en' => 'Tourist office advisor', 'keywords' => 'office tourisme'],
                    ['label_fr' => 'Chargé de promotion destination', 'label_en' => 'Destination promotion officer', 'keywords' => 'promotion destination'],
                    ['label_fr' => 'Animateur(trice) tourisme / loisirs', 'label_en' => 'Tourism / leisure animator', 'keywords' => 'animateur tourisme'],
                    ['label_fr' => 'Responsable office / OT', 'label_en' => 'Tourist office manager', 'keywords' => 'responsable ot'],
                ],
            ],
            [
                'slug' => 'tourism-transport-host',
                'name_fr' => 'Transport touristique / Accueil aéroport-gare',
                'name_en' => 'Tourist Transport / Airport-Station Welcome',
                'suggestions' => [
                    ['label_fr' => 'Chauffeur tourisme / minibus', 'label_en' => 'Tour / minibus driver', 'keywords' => 'chauffeur tourisme'],
                    ['label_fr' => 'Agent d\'accueil aéroport / gare', 'label_en' => 'Airport / station greeter', 'keywords' => 'accueil aeroport'],
                    ['label_fr' => 'Steward / hôtesse transport', 'label_en' => 'Transport steward / hostess', 'keywords' => 'hotesse transport'],
                    ['label_fr' => 'Responsable flotte / transfers', 'label_en' => 'Fleet / transfers manager', 'keywords' => 'flotte transfers'],
                ],
            ],

            // ——— Hébergements alternatifs ———
            [
                'slug' => 'hospitality-alt-lodging',
                'name_fr' => 'Résidences / Camping / Location saisonnière',
                'name_en' => 'Residences / Camping / Short-term Rentals',
                'suggestions' => [
                    ['label_fr' => 'Responsable résidence de tourisme', 'label_en' => 'Tourist residence manager', 'keywords' => 'residence tourisme'],
                    ['label_fr' => 'Gestionnaire camping / glamping', 'label_en' => 'Campsite / glamping manager', 'keywords' => 'camping glamping'],
                    ['label_fr' => 'Property manager / location courte durée', 'label_en' => 'Short-term rental property manager', 'keywords' => 'property manager airbnb'],
                    ['label_fr' => 'Gérant gîte / maison d\'hôtes / riad', 'label_en' => 'Guesthouse / riad manager', 'keywords' => 'gite riad maison hotes'],
                ],
            ],

            // ——— Qualité, hygiène, support ———
            [
                'slug' => 'hospitality-quality-hygiene',
                'name_fr' => 'Qualité / Hygiène / HACCP restauration',
                'name_en' => 'Quality / Hygiene / HACCP',
                'suggestions' => [
                    ['label_fr' => 'Responsable qualité restauration', 'label_en' => 'Catering quality manager', 'keywords' => 'qualite restauration'],
                    ['label_fr' => 'Responsable hygiène / HACCP', 'label_en' => 'Hygiene / HACCP officer', 'keywords' => 'haccp hygiene'],
                    ['label_fr' => 'Contrôleur qualité cuisine', 'label_en' => 'Kitchen quality controller', 'keywords' => 'controle qualite'],
                    ['label_fr' => 'Référent PMS / standards enseigne', 'label_en' => 'Brand standards / PMS referee', 'keywords' => 'standards enseigne'],
                ],
            ],
            [
                'slug' => 'hospitality-procurement',
                'name_fr' => 'Achats / Économat / Cost control',
                'name_en' => 'Procurement / Stores / Cost Control',
                'suggestions' => [
                    ['label_fr' => 'Économe / responsable économat', 'label_en' => 'Storekeeper / cost controller', 'keywords' => 'economat'],
                    ['label_fr' => 'Acheteur restauration / hôtel', 'label_en' => 'Hotel / F&B buyer', 'keywords' => 'achats restauration'],
                    ['label_fr' => 'Cost controller F&B', 'label_en' => 'F&B cost controller', 'keywords' => 'cost control fnb'],
                    ['label_fr' => 'Gestionnaire stocks cuisine', 'label_en' => 'Kitchen inventory clerk', 'keywords' => 'stocks cuisine'],
                ],
            ],
            [
                'slug' => 'hospitality-finance-control',
                'name_fr' => 'Comptabilité / Contrôle de gestion hôtellerie',
                'name_en' => 'Hospitality Accounting / Controlling',
                'suggestions' => [
                    ['label_fr' => 'Comptable hôtelier', 'label_en' => 'Hotel accountant', 'keywords' => 'comptable hotelier'],
                    ['label_fr' => 'Contrôleur de gestion hôtel', 'label_en' => 'Hotel controller', 'keywords' => 'controle gestion hotel'],
                    ['label_fr' => 'Night audit / caisse hôtelière', 'label_en' => 'Night audit / hotel cashier', 'keywords' => 'night audit caisse'],
                    ['label_fr' => 'Credit manager / comptes clients', 'label_en' => 'Credit / guest accounts', 'keywords' => 'credit manager'],
                ],
            ],
            [
                'slug' => 'hospitality-hr-training',
                'name_fr' => 'RH / Formation hôtellerie-restauration',
                'name_en' => 'Hospitality HR / Training',
                'suggestions' => [
                    ['label_fr' => 'Responsable RH hôtel / restaurant', 'label_en' => 'Hotel / restaurant HR', 'keywords' => 'rh hotel'],
                    ['label_fr' => 'Formateur(trice) hôtellerie-restauration', 'label_en' => 'Hospitality trainer', 'keywords' => 'formateur hotellerie'],
                    ['label_fr' => 'Chargé recrutement saisonnier', 'label_en' => 'Seasonal recruiter', 'keywords' => 'recrutement saisonnier'],
                    ['label_fr' => 'Gestionnaire planning / plannings équipes', 'label_en' => 'Roster / schedule planner', 'keywords' => 'planning equipes'],
                ],
            ],
            [
                'slug' => 'hospitality-it-pms',
                'name_fr' => 'SI hôtelier / PMS / Digital',
                'name_en' => 'Hotel IT / PMS / Digital',
                'suggestions' => [
                    ['label_fr' => 'Administrateur PMS / Opera / etc.', 'label_en' => 'PMS administrator', 'keywords' => 'pms opera'],
                    ['label_fr' => 'Support SI hôtelier', 'label_en' => 'Hotel IT support', 'keywords' => 'si hotelier'],
                    ['label_fr' => 'Chef de projet digital hospitality', 'label_en' => 'Hospitality digital PM', 'keywords' => 'digital hospitality'],
                    ['label_fr' => 'Data analyst hôtel / tourisme', 'label_en' => 'Hotel / tourism data analyst', 'keywords' => 'data hotel'],
                ],
            ],
            [
                'slug' => 'hospitality-admin-support',
                'name_fr' => 'Assistant(e) / Support administratif hospitality',
                'name_en' => 'Hospitality Admin / Support Assistant',
                'suggestions' => [
                    ['label_fr' => 'Assistant(e) de direction hôtel', 'label_en' => 'Hotel executive assistant', 'keywords' => 'assistant direction'],
                    ['label_fr' => 'Assistant(e) commercial(e) / réservations', 'label_en' => 'Sales / reservations assistant', 'keywords' => 'assistant reservations'],
                    ['label_fr' => 'Secrétaire / ADV restauration', 'label_en' => 'Catering admin assistant', 'keywords' => 'adv restauration'],
                    ['label_fr' => 'Agent administratif tourisme', 'label_en' => 'Tourism admin clerk', 'keywords' => 'admin tourisme'],
                ],
            ],
            [
                'slug' => 'hospitality-security',
                'name_fr' => 'Sûreté / Sécurité établissement',
                'name_en' => 'Hotel / Venue Security',
                'suggestions' => [
                    ['label_fr' => 'Responsable sûreté hôtel', 'label_en' => 'Hotel security manager', 'keywords' => 'surete hotel'],
                    ['label_fr' => 'Agent de sécurité / vigile', 'label_en' => 'Security officer', 'keywords' => 'agent securite'],
                    ['label_fr' => 'Agent SSIAP / incendie', 'label_en' => 'Fire safety officer', 'keywords' => 'ssiap incendie'],
                    ['label_fr' => 'Contrôleur d\'accès / événements', 'label_en' => 'Access / event controller', 'keywords' => 'controle acces'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'transport-logistics',
        'name_fr' => 'Transport & logistique',
        'name_en' => 'Transport & logistics',
        'professions' => [
            // ——— Direction & encadrement ———
            [
                'slug' => 'logistics-manager',
                'name_fr' => 'Directeur / Responsable logistique',
                'name_en' => 'Logistics Director / Manager',
                'suggestions' => [
                    ['label_fr' => 'Directeur logistique', 'label_en' => 'Logistics director', 'keywords' => 'directeur logistique'],
                    ['label_fr' => 'Responsable logistique', 'label_en' => 'Logistics manager', 'keywords' => 'responsable logistique'],
                    ['label_fr' => 'Directeur d\'exploitation transport', 'label_en' => 'Transport operations director', 'keywords' => 'exploitation transport'],
                    ['label_fr' => 'Responsable plateforme / hub', 'label_en' => 'Hub / platform manager', 'keywords' => 'plateforme hub'],
                ],
            ],
            [
                'slug' => 'supply-chain',
                'name_fr' => 'Supply chain manager / Directeur supply chain',
                'name_en' => 'Supply Chain Manager / Director',
                'suggestions' => [
                    ['label_fr' => 'Supply chain manager', 'label_en' => 'Supply chain manager', 'keywords' => 'supply chain'],
                    ['label_fr' => 'Directeur supply chain', 'label_en' => 'Supply chain director', 'keywords' => 'directeur supply chain'],
                    ['label_fr' => 'Responsable S&OP / planification', 'label_en' => 'S&OP / planning manager', 'keywords' => 'sop planification'],
                    ['label_fr' => 'Demand planner / prévisionniste', 'label_en' => 'Demand planner', 'keywords' => 'demand planning'],
                ],
            ],
            [
                'slug' => 'transport-fleet-director',
                'name_fr' => 'Directeur / Responsable de flotte',
                'name_en' => 'Fleet Director / Manager',
                'suggestions' => [
                    ['label_fr' => 'Directeur de flotte', 'label_en' => 'Fleet director', 'keywords' => 'directeur flotte'],
                    ['label_fr' => 'Responsable parc véhicules', 'label_en' => 'Vehicle fleet manager', 'keywords' => 'parc vehicules'],
                    ['label_fr' => 'Fleet manager / TCO', 'label_en' => 'Fleet manager / TCO', 'keywords' => 'fleet tco'],
                    ['label_fr' => 'Responsable maintenance flotte', 'label_en' => 'Fleet maintenance manager', 'keywords' => 'maintenance flotte'],
                ],
            ],

            // ——— Transport / exploitation ———
            [
                'slug' => 'transport-planner',
                'name_fr' => 'Planificateur / Exploitant transport',
                'name_en' => 'Transport Planner / Dispatcher',
                'suggestions' => [
                    ['label_fr' => 'Planificateur transport', 'label_en' => 'Transport planner', 'keywords' => 'planificateur transport tms'],
                    ['label_fr' => 'Exploitant transport / dispatcher', 'label_en' => 'Transport dispatcher', 'keywords' => 'exploitant dispatcher'],
                    ['label_fr' => 'Régulateur de trafic / flux', 'label_en' => 'Traffic / flow controller', 'keywords' => 'regulateur trafic'],
                    ['label_fr' => 'Optimiseur tournées / routing', 'label_en' => 'Route optimiser', 'keywords' => 'tournees routing'],
                ],
            ],
            [
                'slug' => 'transport-road-ops',
                'name_fr' => 'Exploitation transport routier',
                'name_en' => 'Road Transport Operations',
                'suggestions' => [
                    ['label_fr' => 'Responsable exploitation routière', 'label_en' => 'Road operations manager', 'keywords' => 'exploitation routiere'],
                    ['label_fr' => 'Chef d\'atelier transport', 'label_en' => 'Transport workshop supervisor', 'keywords' => 'chef atelier'],
                    ['label_fr' => 'Responsable messagerie / express', 'label_en' => 'Parcel / express manager', 'keywords' => 'messagerie express'],
                    ['label_fr' => 'Responsable affrètement', 'label_en' => 'Freight brokerage manager', 'keywords' => 'affretement'],
                ],
            ],
            [
                'slug' => 'transport-freight-forwarder',
                'name_fr' => 'Transitaire / Commissionnaire de transport',
                'name_en' => 'Freight Forwarder / Transport Broker',
                'suggestions' => [
                    ['label_fr' => 'Transitaire international', 'label_en' => 'International freight forwarder', 'keywords' => 'transitaire'],
                    ['label_fr' => 'Commissionnaire de transport', 'label_en' => 'Transport commission agent', 'keywords' => 'commissionnaire'],
                    ['label_fr' => 'Agent maritime / shipping agent', 'label_en' => 'Shipping agent', 'keywords' => 'agent maritime'],
                    ['label_fr' => 'Chargé d\'opérations fret', 'label_en' => 'Freight operations officer', 'keywords' => 'operations fret'],
                    ['label_fr' => 'Customer service forwarding', 'label_en' => 'Forwarding customer service', 'keywords' => 'customer service forwarding'],
                ],
            ],
            [
                'slug' => 'transport-multimodal',
                'name_fr' => 'Fret multimodal / Maritime / Aérien / Ferroviaire',
                'name_en' => 'Multimodal / Sea / Air / Rail Freight',
                'suggestions' => [
                    ['label_fr' => 'Responsable fret maritime / LCL-FCL', 'label_en' => 'Sea freight manager', 'keywords' => 'fret maritime'],
                    ['label_fr' => 'Responsable fret aérien', 'label_en' => 'Air freight manager', 'keywords' => 'fret aerien'],
                    ['label_fr' => 'Responsable fret ferroviaire / combiné', 'label_en' => 'Rail / combined freight manager', 'keywords' => 'fret ferroviaire'],
                    ['label_fr' => 'Coordinateur multimodal / intermodal', 'label_en' => 'Multimodal / intermodal coordinator', 'keywords' => 'multimodal intermodal'],
                ],
            ],
            [
                'slug' => 'transport-customs',
                'name_fr' => 'Douane / Déclarant en douane',
                'name_en' => 'Customs / Customs Broker',
                'suggestions' => [
                    ['label_fr' => 'Déclarant en douane', 'label_en' => 'Customs declarant', 'keywords' => 'declarant douane'],
                    ['label_fr' => 'Responsable douane / compliance', 'label_en' => 'Customs / compliance manager', 'keywords' => 'douane compliance'],
                    ['label_fr' => 'Agent en douane / formalités', 'label_en' => 'Customs clerk', 'keywords' => 'formalites douane'],
                    ['label_fr' => 'Trade compliance / export control', 'label_en' => 'Trade compliance / export control', 'keywords' => 'trade compliance'],
                ],
            ],

            // ——— Entrepôt / stocks ———
            [
                'slug' => 'transport-warehouse-manager',
                'name_fr' => 'Responsable d\'entrepôt / Site logistique',
                'name_en' => 'Warehouse / Logistics Site Manager',
                'suggestions' => [
                    ['label_fr' => 'Responsable d\'entrepôt', 'label_en' => 'Warehouse manager', 'keywords' => 'responsable entrepot'],
                    ['label_fr' => 'Chef d\'équipe entrepôt', 'label_en' => 'Warehouse team leader', 'keywords' => 'chef equipe entrepot'],
                    ['label_fr' => 'Responsable site 3PL / logistique', 'label_en' => '3PL site manager', 'keywords' => '3pl site'],
                    ['label_fr' => 'Responsable cross-dock / hub', 'label_en' => 'Cross-dock / hub manager', 'keywords' => 'cross dock'],
                ],
            ],
            [
                'slug' => 'transport-warehouse-ops',
                'name_fr' => 'Préparateur / Magasinier / Cariste',
                'name_en' => 'Order Picker / Storekeeper / Forklift Operator',
                'suggestions' => [
                    ['label_fr' => 'Préparateur(trice) de commandes', 'label_en' => 'Order picker', 'keywords' => 'preparation commandes'],
                    ['label_fr' => 'Magasinier(ère)', 'label_en' => 'Storekeeper', 'keywords' => 'magasinier'],
                    ['label_fr' => 'Cariste / conducteur engins', 'label_en' => 'Forklift / MHE operator', 'keywords' => 'cariste engins'],
                    ['label_fr' => 'Réceptionnaire / contrôleur réception', 'label_en' => 'Receiving clerk', 'keywords' => 'receptionnaire'],
                    ['label_fr' => 'Agent d\'expédition / packing', 'label_en' => 'Shipping / packing clerk', 'keywords' => 'expedition packing'],
                ],
            ],
            [
                'slug' => 'transport-inventory',
                'name_fr' => 'Gestionnaire stocks / Inventaires / WMS',
                'name_en' => 'Inventory / WMS Controller',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire de stocks', 'label_en' => 'Inventory controller', 'keywords' => 'gestionnaire stocks'],
                    ['label_fr' => 'Responsable inventaires', 'label_en' => 'Inventory manager', 'keywords' => 'inventaires'],
                    ['label_fr' => 'Administrateur WMS', 'label_en' => 'WMS administrator', 'keywords' => 'wms'],
                    ['label_fr' => 'Analyste stocks / rotation', 'label_en' => 'Stock / turnover analyst', 'keywords' => 'rotation stocks'],
                ],
            ],
            [
                'slug' => 'transport-last-mile',
                'name_fr' => 'Last mile / Distribution urbaine / Messagerie',
                'name_en' => 'Last Mile / Urban Distribution / Courier',
                'suggestions' => [
                    ['label_fr' => 'Responsable last mile', 'label_en' => 'Last mile manager', 'keywords' => 'last mile'],
                    ['label_fr' => 'Responsable agence messagerie', 'label_en' => 'Courier depot manager', 'keywords' => 'agence messagerie'],
                    ['label_fr' => 'Coordinateur livraisons urbaines', 'label_en' => 'Urban delivery coordinator', 'keywords' => 'livraisons urbaines'],
                    ['label_fr' => 'Responsable locker / points relais', 'label_en' => 'Locker / pickup points manager', 'keywords' => 'points relais'],
                ],
            ],

            // ——— Conducteurs / métiers terrain ———
            [
                'slug' => 'transport-truck-driver',
                'name_fr' => 'Chauffeur poids lourd / Routier',
                'name_en' => 'Heavy Goods / Long-Haul Driver',
                'suggestions' => [
                    ['label_fr' => 'Chauffeur PL / poids lourd', 'label_en' => 'HGV / truck driver', 'keywords' => 'chauffeur pl'],
                    ['label_fr' => 'Chauffeur longue distance / international', 'label_en' => 'Long-haul / international driver', 'keywords' => 'longue distance'],
                    ['label_fr' => 'Chauffeur citerne / ADR', 'label_en' => 'Tanker / ADR driver', 'keywords' => 'citerne adr'],
                    ['label_fr' => 'Chauffeur frigorifique / ATP', 'label_en' => 'Reefer / ATP driver', 'keywords' => 'frigorifique atp'],
                    ['label_fr' => 'Chauffeur benne / chantier', 'label_en' => 'Dump truck / site driver', 'keywords' => 'benne chantier'],
                ],
            ],
            [
                'slug' => 'transport-light-driver',
                'name_fr' => 'Chauffeur VL / Livreur / Coursier',
                'name_en' => 'Light Vehicle / Delivery / Courier Driver',
                'suggestions' => [
                    ['label_fr' => 'Chauffeur-livreur VL', 'label_en' => 'Van delivery driver', 'keywords' => 'chauffeur livreur vl'],
                    ['label_fr' => 'Livreur(se) à domicile', 'label_en' => 'Home delivery driver', 'keywords' => 'livraison domicile'],
                    ['label_fr' => 'Coursier / messager', 'label_en' => 'Courier / messenger', 'keywords' => 'coursier'],
                    ['label_fr' => 'Chauffeur SPL / léger utilitaire', 'label_en' => 'Light commercial driver', 'keywords' => 'utilitaire'],
                ],
            ],
            [
                'slug' => 'transport-passenger',
                'name_fr' => 'Transport de personnes / Autocar / Taxi',
                'name_en' => 'Passenger Transport / Coach / Taxi',
                'suggestions' => [
                    ['label_fr' => 'Conducteur d\'autocar / car', 'label_en' => 'Coach / bus driver', 'keywords' => 'autocar car'],
                    ['label_fr' => 'Conducteur de bus urbain', 'label_en' => 'City bus driver', 'keywords' => 'bus urbain'],
                    ['label_fr' => 'Chauffeur VTC / taxi', 'label_en' => 'Private hire / taxi driver', 'keywords' => 'vtc taxi'],
                    ['label_fr' => 'Conducteur scolaire / personnel', 'label_en' => 'School / staff shuttle driver', 'keywords' => 'transport scolaire'],
                    ['label_fr' => 'Responsable exploitation voyageurs', 'label_en' => 'Passenger ops manager', 'keywords' => 'exploitation voyageurs'],
                ],
            ],
            [
                'slug' => 'transport-handling',
                'name_fr' => 'Manutention / Quai / Dock',
                'name_en' => 'Handling / Dock Operations',
                'suggestions' => [
                    ['label_fr' => 'Agent de quai / dock', 'label_en' => 'Dock / quay worker', 'keywords' => 'agent quai'],
                    ['label_fr' => 'Manutentionnaire', 'label_en' => 'Handler / labourer', 'keywords' => 'manutentionnaire'],
                    ['label_fr' => 'Chef de quai', 'label_en' => 'Dock supervisor', 'keywords' => 'chef de quai'],
                    ['label_fr' => 'Agent de chargement / déchargement', 'label_en' => 'Loading / unloading agent', 'keywords' => 'chargement dechargement'],
                ],
            ],

            // ——— Modes spécialisés ———
            [
                'slug' => 'transport-port-ops',
                'name_fr' => 'Exploitation portuaire / Terminal',
                'name_en' => 'Port / Terminal Operations',
                'suggestions' => [
                    ['label_fr' => 'Responsable terminal portuaire', 'label_en' => 'Port terminal manager', 'keywords' => 'terminal portuaire'],
                    ['label_fr' => 'Agent d\'escale / operations port', 'label_en' => 'Port operations / ship agent', 'keywords' => 'agent escale'],
                    ['label_fr' => 'Planificateur navires / stevedoring', 'label_en' => 'Vessel planner / stevedoring', 'keywords' => 'stevedoring'],
                    ['label_fr' => 'Conducteur engins portuaires / RTG', 'label_en' => 'Port equipment / RTG operator', 'keywords' => 'engins portuaires'],
                ],
            ],
            [
                'slug' => 'transport-air-ops',
                'name_fr' => 'Exploitation aérienne / Cargo aéroport',
                'name_en' => 'Air Cargo / Airport Operations',
                'suggestions' => [
                    ['label_fr' => 'Responsable cargo aérien', 'label_en' => 'Air cargo manager', 'keywords' => 'cargo aerien'],
                    ['label_fr' => 'Agent cargo / handling aéroport', 'label_en' => 'Airport cargo / handling agent', 'keywords' => 'handling aeroport'],
                    ['label_fr' => 'Agent d\'escale fret', 'label_en' => 'Freight ramp agent', 'keywords' => 'escale fret'],
                    ['label_fr' => 'Responsable entrepôt cargo', 'label_en' => 'Cargo warehouse manager', 'keywords' => 'entrepot cargo'],
                ],
            ],
            [
                'slug' => 'transport-rail-ops',
                'name_fr' => 'Exploitation ferroviaire / Fret rail',
                'name_en' => 'Rail Operations / Rail Freight',
                'suggestions' => [
                    ['label_fr' => 'Responsable exploitation ferroviaire', 'label_en' => 'Rail operations manager', 'keywords' => 'exploitation ferroviaire'],
                    ['label_fr' => 'Agent circulation / aiguillage', 'label_en' => 'Traffic / signalling agent', 'keywords' => 'circulation aiguillage'],
                    ['label_fr' => 'Conducteur de train / loco', 'label_en' => 'Train / locomotive driver', 'keywords' => 'conducteur train'],
                    ['label_fr' => 'Agent fret ferroviaire', 'label_en' => 'Rail freight agent', 'keywords' => 'fret rail'],
                ],
            ],
            [
                'slug' => 'transport-cold-chain',
                'name_fr' => 'Chaîne du froid / Pharma / Food logistics',
                'name_en' => 'Cold Chain / Pharma / Food Logistics',
                'suggestions' => [
                    ['label_fr' => 'Responsable chaîne du froid', 'label_en' => 'Cold chain manager', 'keywords' => 'chaine du froid'],
                    ['label_fr' => 'Logisticien pharma / GDP', 'label_en' => 'Pharma / GDP logistics', 'keywords' => 'pharma gdp'],
                    ['label_fr' => 'Technicien froid / monitoring température', 'label_en' => 'Cold / temperature monitoring tech', 'keywords' => 'monitoring temperature'],
                    ['label_fr' => 'Responsable logistique agroalimentaire', 'label_en' => 'Food logistics manager', 'keywords' => 'logistique agroalimentaire'],
                ],
            ],

            // ——— Achats, commercial, planning ———
            [
                'slug' => 'transport-procurement',
                'name_fr' => 'Acheteur transport / Approvisionnements',
                'name_en' => 'Transport Buyer / Procurement',
                'suggestions' => [
                    ['label_fr' => 'Acheteur transport / freight', 'label_en' => 'Transport / freight buyer', 'keywords' => 'acheteur transport'],
                    ['label_fr' => 'Acheteur logistique / 3PL', 'label_en' => 'Logistics / 3PL buyer', 'keywords' => 'acheteur 3pl'],
                    ['label_fr' => 'Approvisionneur / buyer planner', 'label_en' => 'Replenishment buyer / planner', 'keywords' => 'approvisionneur'],
                    ['label_fr' => 'Category buyer packaging / emballage', 'label_en' => 'Packaging buyer', 'keywords' => 'emballage packaging'],
                ],
            ],
            [
                'slug' => 'transport-sales',
                'name_fr' => 'Commercial transport / Affréteur',
                'name_en' => 'Transport Sales / Freight Broker',
                'suggestions' => [
                    ['label_fr' => 'Commercial transport / logistique', 'label_en' => 'Transport / logistics sales', 'keywords' => 'commercial transport'],
                    ['label_fr' => 'Affréteur / broker fret', 'label_en' => 'Freight broker', 'keywords' => 'affreteur broker'],
                    ['label_fr' => 'Key Account Manager logistique', 'label_en' => 'Logistics KAM', 'keywords' => 'kam logistique'],
                    ['label_fr' => 'Business developer 3PL / 4PL', 'label_en' => '3PL / 4PL business developer', 'keywords' => 'bizdev 3pl'],
                ],
            ],
            [
                'slug' => 'transport-customer-service',
                'name_fr' => 'Customer service / Suivi livraisons',
                'name_en' => 'Customer Service / Shipment Tracking',
                'suggestions' => [
                    ['label_fr' => 'Chargé customer service logistique', 'label_en' => 'Logistics customer service', 'keywords' => 'customer service'],
                    ['label_fr' => 'Gestionnaire litiges / reclamations transport', 'label_en' => 'Transport claims officer', 'keywords' => 'litiges transport'],
                    ['label_fr' => 'Track & trace / suivi expéditions', 'label_en' => 'Track & trace officer', 'keywords' => 'track trace'],
                    ['label_fr' => 'ADV transport / order desk', 'label_en' => 'Transport order desk', 'keywords' => 'adv transport'],
                ],
            ],

            // ——— Qualité, HSE, support ———
            [
                'slug' => 'transport-qhse',
                'name_fr' => 'QHSE / Sécurité transport & entrepôt',
                'name_en' => 'QHSE / Transport & Warehouse Safety',
                'suggestions' => [
                    ['label_fr' => 'Responsable QHSE logistique', 'label_en' => 'Logistics QHSE manager', 'keywords' => 'qhse logistique'],
                    ['label_fr' => 'Animateur sécurité entrepôt', 'label_en' => 'Warehouse safety officer', 'keywords' => 'securite entrepot'],
                    ['label_fr' => 'Conseiller sécurité ADR / matières dangereuses', 'label_en' => 'ADR / dangerous goods advisor', 'keywords' => 'adr matieres dangereuses'],
                    ['label_fr' => 'Auditeur qualité / ISO transport', 'label_en' => 'Quality / ISO transport auditor', 'keywords' => 'audit iso transport'],
                ],
            ],
            [
                'slug' => 'transport-maintenance',
                'name_fr' => 'Maintenance véhicules / Atelier',
                'name_en' => 'Vehicle Maintenance / Workshop',
                'suggestions' => [
                    ['label_fr' => 'Mécanicien poids lourd', 'label_en' => 'HGV mechanic', 'keywords' => 'mecanicien pl'],
                    ['label_fr' => 'Technicien diagnostic flotte', 'label_en' => 'Fleet diagnostic technician', 'keywords' => 'diagnostic flotte'],
                    ['label_fr' => 'Chef d\'atelier mécanique', 'label_en' => 'Workshop foreman', 'keywords' => 'chef atelier mecanique'],
                    ['label_fr' => 'Électromécanicien / freinage / pneumatiques', 'label_en' => 'Electromechanic / brakes / tyres', 'keywords' => 'freinage pneumatiques'],
                ],
            ],
            [
                'slug' => 'transport-digital-tms',
                'name_fr' => 'TMS / WMS / Digital logistique',
                'name_en' => 'TMS / WMS / Logistics Digital',
                'suggestions' => [
                    ['label_fr' => 'Consultant / admin TMS', 'label_en' => 'TMS consultant / admin', 'keywords' => 'tms'],
                    ['label_fr' => 'Consultant / admin WMS', 'label_en' => 'WMS consultant / admin', 'keywords' => 'wms'],
                    ['label_fr' => 'Data analyst supply chain', 'label_en' => 'Supply chain data analyst', 'keywords' => 'data supply chain'],
                    ['label_fr' => 'Chef de projet SI logistique', 'label_en' => 'Logistics IT project manager', 'keywords' => 'projet si logistique'],
                ],
            ],
            [
                'slug' => 'transport-finance-control',
                'name_fr' => 'Contrôle de gestion / Facturation transport',
                'name_en' => 'Controlling / Transport Billing',
                'suggestions' => [
                    ['label_fr' => 'Contrôleur de gestion logistique', 'label_en' => 'Logistics controller', 'keywords' => 'controle gestion'],
                    ['label_fr' => 'Gestionnaire facturation transport', 'label_en' => 'Transport billing officer', 'keywords' => 'facturation transport'],
                    ['label_fr' => 'Analyste coûts / freight audit', 'label_en' => 'Cost / freight audit analyst', 'keywords' => 'freight audit'],
                    ['label_fr' => 'Credit controller transport', 'label_en' => 'Transport credit controller', 'keywords' => 'credit controller'],
                ],
            ],
            [
                'slug' => 'transport-hr-training',
                'name_fr' => 'RH / Formation transport & logistique',
                'name_en' => 'Transport & Logistics HR / Training',
                'suggestions' => [
                    ['label_fr' => 'Responsable RH transport / entrepôt', 'label_en' => 'Transport / warehouse HR', 'keywords' => 'rh transport'],
                    ['label_fr' => 'Formateur FIMO / FCO / sécurité', 'label_en' => 'Driver / safety trainer', 'keywords' => 'fimo fco'],
                    ['label_fr' => 'Chargé recrutement chauffeurs', 'label_en' => 'Driver recruiter', 'keywords' => 'recrutement chauffeurs'],
                    ['label_fr' => 'Gestionnaire planning conducteurs', 'label_en' => 'Driver roster planner', 'keywords' => 'planning conducteurs'],
                ],
            ],
            [
                'slug' => 'transport-admin-support',
                'name_fr' => 'Assistant(e) / Support administratif transport',
                'name_en' => 'Transport Admin / Support Assistant',
                'suggestions' => [
                    ['label_fr' => 'Assistant(e) exploitation transport', 'label_en' => 'Transport ops assistant', 'keywords' => 'assistant exploitation'],
                    ['label_fr' => 'Assistant(e) import-export', 'label_en' => 'Import-export assistant', 'keywords' => 'assistant import export'],
                    ['label_fr' => 'Gestionnaire administratif documents de bord', 'label_en' => 'Transport documents clerk', 'keywords' => 'documents de bord'],
                    ['label_fr' => 'Secrétaire / ADV logistique', 'label_en' => 'Logistics admin / order desk', 'keywords' => 'adv logistique'],
                ],
            ],
            [
                'slug' => 'transport-security',
                'name_fr' => 'Sûreté / Sécurité sites & marchandises',
                'name_en' => 'Site & Cargo Security',
                'suggestions' => [
                    ['label_fr' => 'Responsable sûreté logistique', 'label_en' => 'Logistics security manager', 'keywords' => 'surete logistique'],
                    ['label_fr' => 'Agent de sûreté entrepôt / site', 'label_en' => 'Warehouse / site security', 'keywords' => 'surete entrepot'],
                    ['label_fr' => 'Contrôleur accès / vidéosurveillance', 'label_en' => 'Access / CCTV controller', 'keywords' => 'controle acces'],
                    ['label_fr' => 'Enquêteur pertes / litiges cargaison', 'label_en' => 'Cargo loss / claim investigator', 'keywords' => 'pertes cargaison'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'consulting',
        'name_fr' => 'Conseil & services aux entreprises',
        'name_en' => 'Consulting & professional services',
        'professions' => [
            // ——— Conseil en management / stratégie ———
            [
                'slug' => 'management-consultant',
                'name_fr' => 'Consultant en management / stratégie',
                'name_en' => 'Management / Strategy Consultant',
                'suggestions' => [
                    ['label_fr' => 'Consultant stratégie', 'label_en' => 'Strategy consultant', 'keywords' => 'strategie conseil'],
                    ['label_fr' => 'Consultant organisation / transformation', 'label_en' => 'Organization / transformation consultant', 'keywords' => 'organisation transformation'],
                    ['label_fr' => 'Consultant operational excellence / lean', 'label_en' => 'Operational excellence / lean consultant', 'keywords' => 'lean excellence'],
                    ['label_fr' => 'Consultant change management', 'label_en' => 'Change management consultant', 'keywords' => 'change management'],
                    ['label_fr' => 'Manager / partner conseil', 'label_en' => 'Consulting manager / partner', 'keywords' => 'partner manager conseil'],
                ],
            ],
            [
                'slug' => 'consulting-strategy-ma',
                'name_fr' => 'Conseil stratégie / M&A / Corporate finance',
                'name_en' => 'Strategy / M&A / Corporate Finance Advisory',
                'suggestions' => [
                    ['label_fr' => 'Consultant M&A / transactions', 'label_en' => 'M&A / transactions advisor', 'keywords' => 'm&a transactions'],
                    ['label_fr' => 'Analyste / associate corporate finance', 'label_en' => 'Corporate finance analyst / associate', 'keywords' => 'corporate finance'],
                    ['label_fr' => 'Due diligence financière / opérationnelle', 'label_en' => 'Financial / operational due diligence', 'keywords' => 'due diligence'],
                    ['label_fr' => 'Valorisation / business plan', 'label_en' => 'Valuation / business plan', 'keywords' => 'valorisation business plan'],
                ],
            ],
            [
                'slug' => 'consulting-it',
                'name_fr' => 'Consultant SI / Digital / ERP',
                'name_en' => 'IT / Digital / ERP Consultant',
                'suggestions' => [
                    ['label_fr' => 'Consultant SI / ERP', 'label_en' => 'IT / ERP consultant', 'keywords' => 'erp sap oracle'],
                    ['label_fr' => 'Consultant transformation digitale', 'label_en' => 'Digital transformation consultant', 'keywords' => 'transformation digitale'],
                    ['label_fr' => 'Business analyst / AMOA', 'label_en' => 'Business analyst / BA', 'keywords' => 'business analyst amoa'],
                    ['label_fr' => 'Consultant cybersécurité / GRC', 'label_en' => 'Cybersecurity / GRC consultant', 'keywords' => 'cybersecurite grc'],
                    ['label_fr' => 'Consultant data / analytics', 'label_en' => 'Data / analytics consultant', 'keywords' => 'data analytics conseil'],
                ],
            ],
            [
                'slug' => 'consulting-rh-talent',
                'name_fr' => 'Consultant RH / Organisation / Talent',
                'name_en' => 'HR / Organization / Talent Consultant',
                'suggestions' => [
                    ['label_fr' => 'Consultant RH / organisation', 'label_en' => 'HR / organization consultant', 'keywords' => 'consultant rh'],
                    ['label_fr' => 'Consultant talent / rémunération', 'label_en' => 'Talent / compensation consultant', 'keywords' => 'remuneration talent'],
                    ['label_fr' => 'Consultant formation / L&D', 'label_en' => 'L&D / training consultant', 'keywords' => 'formation ld'],
                    ['label_fr' => 'Coach professionnel / executive coach', 'label_en' => 'Professional / executive coach', 'keywords' => 'coaching'],
                ],
            ],

            // ——— Audit, expertise comptable, fiscal ———
            [
                'slug' => 'consulting-audit',
                'name_fr' => 'Auditeur / Commissaire aux comptes',
                'name_en' => 'Auditor / Statutory Auditor',
                'suggestions' => [
                    ['label_fr' => 'Auditeur financier (cabinet)', 'label_en' => 'Financial auditor (firm)', 'keywords' => 'auditeur financier'],
                    ['label_fr' => 'Commissaire aux comptes / collaborateur CAC', 'label_en' => 'Statutory auditor / associate', 'keywords' => 'commissaire comptes'],
                    ['label_fr' => 'Auditeur interne (mission conseil)', 'label_en' => 'Internal audit advisor', 'keywords' => 'audit interne'],
                    ['label_fr' => 'Auditeur IT / systèmes', 'label_en' => 'IT systems auditor', 'keywords' => 'audit it'],
                    ['label_fr' => 'Senior / manager audit', 'label_en' => 'Audit senior / manager', 'keywords' => 'manager audit'],
                ],
            ],
            [
                'slug' => 'consulting-chartered-accountant',
                'name_fr' => 'Expert-comptable / Collaborateur cabinet',
                'name_en' => 'Chartered Accountant / Firm Associate',
                'suggestions' => [
                    ['label_fr' => 'Expert-comptable', 'label_en' => 'Chartered accountant', 'keywords' => 'expert comptable'],
                    ['label_fr' => 'Collaborateur expert-comptable', 'label_en' => 'Accounting firm associate', 'keywords' => 'collaborateur ec'],
                    ['label_fr' => 'Responsable de dossier / mission', 'label_en' => 'Engagement / client manager', 'keywords' => 'responsable dossier'],
                    ['label_fr' => 'Comptable cabinet / tenu de comptes', 'label_en' => 'Bookkeeping associate', 'keywords' => 'tenu de comptes'],
                    ['label_fr' => 'Assistant(e) expert-comptable', 'label_en' => 'Accounting assistant (firm)', 'keywords' => 'assistant expert comptable'],
                ],
            ],
            [
                'slug' => 'consulting-tax',
                'name_fr' => 'Fiscaliste / Conseil fiscal',
                'name_en' => 'Tax Advisor / Tax Consultant',
                'suggestions' => [
                    ['label_fr' => 'Fiscaliste / tax advisor', 'label_en' => 'Tax advisor', 'keywords' => 'fiscaliste'],
                    ['label_fr' => 'Conseil fiscal international / transfer pricing', 'label_en' => 'International tax / TP', 'keywords' => 'transfer pricing'],
                    ['label_fr' => 'Fiscalité des entreprises / IS TVA', 'label_en' => 'Corporate tax / VAT', 'keywords' => 'is tva'],
                    ['label_fr' => 'Fiscalité patrimoniale / particuliers', 'label_en' => 'Personal / wealth tax', 'keywords' => 'fiscalite patrimoine'],
                ],
            ],
            [
                'slug' => 'consulting-payroll-social',
                'name_fr' => 'Conseil social / Paie / Externalisation RH',
                'name_en' => 'Social / Payroll / HR Outsourcing',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire de paie (cabinet / BPO)', 'label_en' => 'Payroll officer (firm / BPO)', 'keywords' => 'paie cabinet'],
                    ['label_fr' => 'Conseiller social / droit social', 'label_en' => 'Employment law / social advisor', 'keywords' => 'droit social'],
                    ['label_fr' => 'Responsable externalisation paie / SIRH', 'label_en' => 'Payroll / HRIS outsourcing lead', 'keywords' => 'externalisation paie'],
                    ['label_fr' => 'Chargé déclarations sociales', 'label_en' => 'Social declarations officer', 'keywords' => 'declarations sociales'],
                ],
            ],

            // ——— Juridique ———
            [
                'slug' => 'legal',
                'name_fr' => 'Avocat / Juriste',
                'name_en' => 'Lawyer / Legal Counsel',
                'suggestions' => [
                    ['label_fr' => 'Avocat', 'label_en' => 'Lawyer', 'keywords' => 'avocat'],
                    ['label_fr' => 'Juriste d\'entreprise', 'label_en' => 'In-house counsel', 'keywords' => 'juriste entreprise'],
                    ['label_fr' => 'Avocat d\'affaires / corporate', 'label_en' => 'Corporate lawyer', 'keywords' => 'avocat affaires'],
                    ['label_fr' => 'Avocat contentieux / litiges', 'label_en' => 'Litigation lawyer', 'keywords' => 'contentieux'],
                    ['label_fr' => 'Collaborateur avocat / élève avocat', 'label_en' => 'Associate / trainee lawyer', 'keywords' => 'collaborateur avocat'],
                ],
            ],
            [
                'slug' => 'consulting-legal-specialist',
                'name_fr' => 'Juriste spécialisé / Compliance / Contrats',
                'name_en' => 'Specialized Counsel / Compliance / Contracts',
                'suggestions' => [
                    ['label_fr' => 'Juriste contrats / commercial', 'label_en' => 'Contracts / commercial counsel', 'keywords' => 'juriste contrats'],
                    ['label_fr' => 'Juriste social / droit du travail', 'label_en' => 'Employment counsel', 'keywords' => 'juriste social'],
                    ['label_fr' => 'Compliance officer (cabinet / entreprise)', 'label_en' => 'Compliance officer', 'keywords' => 'compliance'],
                    ['label_fr' => 'DPO / protection des données', 'label_en' => 'DPO / data protection', 'keywords' => 'dpo rgpd'],
                    ['label_fr' => 'Juriste PI / propriété intellectuelle', 'label_en' => 'IP counsel', 'keywords' => 'propriete intellectuelle'],
                ],
            ],
            [
                'slug' => 'consulting-notary-huissier',
                'name_fr' => 'Notaire / Huissier / Officier ministériel',
                'name_en' => 'Notary / Bailiff / Judicial Officer',
                'suggestions' => [
                    ['label_fr' => 'Notaire / collaborateur notarial', 'label_en' => 'Notary / notarial associate', 'keywords' => 'notaire'],
                    ['label_fr' => 'Huissier de justice / collaborateur', 'label_en' => 'Bailiff / associate', 'keywords' => 'huissier'],
                    ['label_fr' => 'Clerc de notaire', 'label_en' => 'Notary clerk', 'keywords' => 'clerc notaire'],
                    ['label_fr' => 'Assistant(e) juridique / parajuriste', 'label_en' => 'Legal assistant / paralegal', 'keywords' => 'parajuriste'],
                ],
            ],

            // ——— RH & recrutement (services) ———
            [
                'slug' => 'hr',
                'name_fr' => 'Responsable / Chargé RH',
                'name_en' => 'HR Manager / HR Officer',
                'suggestions' => [
                    ['label_fr' => 'Responsable RH / DRH', 'label_en' => 'HR manager / CHRO', 'keywords' => 'responsable rh drh'],
                    ['label_fr' => 'Chargé(e) RH / administration du personnel', 'label_en' => 'HR officer / people admin', 'keywords' => 'charge rh'],
                    ['label_fr' => 'Responsable formation / L&D', 'label_en' => 'L&D manager', 'keywords' => 'formation ld'],
                    ['label_fr' => 'HR business partner', 'label_en' => 'HR business partner', 'keywords' => 'hrbp'],
                    ['label_fr' => 'Responsable relations sociales', 'label_en' => 'Employee relations manager', 'keywords' => 'relations sociales'],
                ],
            ],
            [
                'slug' => 'consulting-recruitment',
                'name_fr' => 'Recruteur / Chasseur de têtes / Talent acquisition',
                'name_en' => 'Recruiter / Headhunter / Talent Acquisition',
                'suggestions' => [
                    ['label_fr' => 'Consultant recrutement / cabinet', 'label_en' => 'Recruitment consultant (agency)', 'keywords' => 'consultant recrutement'],
                    ['label_fr' => 'Chargé(e) de candidatures / talent acquisition', 'label_en' => 'Talent acquisition specialist', 'keywords' => 'talent acquisition'],
                    ['label_fr' => 'Chasseur de têtes / executive search', 'label_en' => 'Headhunter / executive search', 'keywords' => 'chasse tete'],
                    ['label_fr' => 'Sourcer / research recruiter', 'label_en' => 'Sourcer / research recruiter', 'keywords' => 'sourcing'],
                    ['label_fr' => 'Chargé intérim / staffing', 'label_en' => 'Temp staffing consultant', 'keywords' => 'interim staffing'],
                ],
            ],
            [
                'slug' => 'consulting-outplacement-rpo',
                'name_fr' => 'Outplacement / RPO / Conseil carrière',
                'name_en' => 'Outplacement / RPO / Career Advisory',
                'suggestions' => [
                    ['label_fr' => 'Consultant outplacement', 'label_en' => 'Outplacement consultant', 'keywords' => 'outplacement'],
                    ['label_fr' => 'Consultant RPO / recrutement délégué', 'label_en' => 'RPO consultant', 'keywords' => 'rpo'],
                    ['label_fr' => 'Conseiller bilans / orientation', 'label_en' => 'Career / skills assessment advisor', 'keywords' => 'bilan competences'],
                    ['label_fr' => 'Consultant mobilité / transition', 'label_en' => 'Mobility / transition consultant', 'keywords' => 'mobilite transition'],
                ],
            ],

            // ——— Autres services professionnels ———
            [
                'slug' => 'consulting-facility-bpo',
                'name_fr' => 'Facility management / BPO / Services généraux',
                'name_en' => 'Facility Management / BPO / General Services',
                'suggestions' => [
                    ['label_fr' => 'Responsable facility management', 'label_en' => 'Facility manager', 'keywords' => 'facility management'],
                    ['label_fr' => 'Responsable BPO / centre de services', 'label_en' => 'BPO / shared services manager', 'keywords' => 'bpo shared services'],
                    ['label_fr' => 'Responsable services généraux', 'label_en' => 'General services manager', 'keywords' => 'services generaux'],
                    ['label_fr' => 'Chargé contrats multiservices', 'label_en' => 'Multiservice contracts officer', 'keywords' => 'multiservices'],
                ],
            ],
            [
                'slug' => 'consulting-real-estate',
                'name_fr' => 'Conseil immobilier d\'entreprise / Transaction',
                'name_en' => 'Corporate Real Estate / Transaction Advisory',
                'suggestions' => [
                    ['label_fr' => 'Consultant immobilier d\'entreprise', 'label_en' => 'Corporate real estate advisor', 'keywords' => 'immobilier entreprise'],
                    ['label_fr' => 'Transaction / brokerage bureaux', 'label_en' => 'Office transaction / brokerage', 'keywords' => 'transaction bureaux'],
                    ['label_fr' => 'Property / asset manager tertiaire', 'label_en' => 'Commercial property / asset manager', 'keywords' => 'property manager'],
                    ['label_fr' => 'Chargé location / commercialisation', 'label_en' => 'Leasing / marketing officer', 'keywords' => 'location commercialisation'],
                ],
            ],
            [
                'slug' => 'consulting-insurance-broker',
                'name_fr' => 'Courtier / Conseil en assurance entreprises',
                'name_en' => 'Corporate Insurance Broker / Advisor',
                'suggestions' => [
                    ['label_fr' => 'Courtier en assurance entreprises', 'label_en' => 'Corporate insurance broker', 'keywords' => 'courtier entreprises'],
                    ['label_fr' => 'Conseil risques / risk advisor', 'label_en' => 'Risk advisor', 'keywords' => 'risk advisor'],
                    ['label_fr' => 'Chargé de clientèle courtage', 'label_en' => 'Brokerage account manager', 'keywords' => 'clientele courtage'],
                    ['label_fr' => 'Souscripteur / placement risques', 'label_en' => 'Risk placement underwriter', 'keywords' => 'placement risques'],
                ],
            ],
            [
                'slug' => 'consulting-training',
                'name_fr' => 'Formateur / Organisme de formation / OF',
                'name_en' => 'Trainer / Training Provider',
                'suggestions' => [
                    ['label_fr' => 'Formateur(trice) professionnel(le)', 'label_en' => 'Corporate trainer', 'keywords' => 'formateur professionnel'],
                    ['label_fr' => 'Responsable organisme de formation', 'label_en' => 'Training provider manager', 'keywords' => 'organisme formation'],
                    ['label_fr' => 'Ingénieur pédagogique / conception', 'label_en' => 'Instructional designer', 'keywords' => 'ingenieur pedagogique'],
                    ['label_fr' => 'Commercial formation / B2B learning', 'label_en' => 'Training sales / B2B learning', 'keywords' => 'commercial formation'],
                ],
            ],
            [
                'slug' => 'consulting-quality-iso',
                'name_fr' => 'Conseil qualité / Certification / ISO',
                'name_en' => 'Quality / Certification / ISO Advisory',
                'suggestions' => [
                    ['label_fr' => 'Consultant qualité / ISO', 'label_en' => 'Quality / ISO consultant', 'keywords' => 'consultant iso'],
                    ['label_fr' => 'Auditeur certification / organisme certificateur', 'label_en' => 'Certification auditor', 'keywords' => 'auditeur certification'],
                    ['label_fr' => 'Consultant QHSE / systèmes de management', 'label_en' => 'QHSE / management systems consultant', 'keywords' => 'qhse systemes'],
                    ['label_fr' => 'Consultant RSE / ESG reporting', 'label_en' => 'CSR / ESG reporting consultant', 'keywords' => 'rse esg'],
                ],
            ],
            [
                'slug' => 'consulting-security-investigation',
                'name_fr' => 'Sûreté / Investigation / Intelligence économique',
                'name_en' => 'Security / Investigation / Business Intelligence',
                'suggestions' => [
                    ['label_fr' => 'Consultant sûreté / security advisor', 'label_en' => 'Security advisor', 'keywords' => 'consultant surete'],
                    ['label_fr' => 'Enquêteur / investigator privé', 'label_en' => 'Private investigator', 'keywords' => 'enqueteur'],
                    ['label_fr' => 'Analyste intelligence économique', 'label_en' => 'Competitive intelligence analyst', 'keywords' => 'intelligence economique'],
                    ['label_fr' => 'Consultant fraude / forensic', 'label_en' => 'Fraud / forensic consultant', 'keywords' => 'fraude forensic'],
                ],
            ],
            [
                'slug' => 'consulting-translation-interp',
                'name_fr' => 'Traducteur / Interprète / Services linguistiques',
                'name_en' => 'Translator / Interpreter / Language Services',
                'suggestions' => [
                    ['label_fr' => 'Traducteur(trice) professionnel(le)', 'label_en' => 'Professional translator', 'keywords' => 'traducteur'],
                    ['label_fr' => 'Interprète de conférence', 'label_en' => 'Conference interpreter', 'keywords' => 'interprete'],
                    ['label_fr' => 'Traducteur juridique / assermenté', 'label_en' => 'Legal / sworn translator', 'keywords' => 'traducteur assermente'],
                    ['label_fr' => 'Project manager agence de traduction', 'label_en' => 'Translation agency PM', 'keywords' => 'agence traduction'],
                ],
            ],

            // ——— Encadrement cabinet & support ———
            [
                'slug' => 'consulting-partner-director',
                'name_fr' => 'Associé / Directeur de cabinet / Practice lead',
                'name_en' => 'Partner / Firm Director / Practice Lead',
                'suggestions' => [
                    ['label_fr' => 'Associé / partner', 'label_en' => 'Partner', 'keywords' => 'associe partner'],
                    ['label_fr' => 'Directeur de cabinet / managing director', 'label_en' => 'Managing director', 'keywords' => 'directeur cabinet'],
                    ['label_fr' => 'Practice leader / responsable métier', 'label_en' => 'Practice leader', 'keywords' => 'practice leader'],
                    ['label_fr' => 'Directeur de mission / engagement director', 'label_en' => 'Engagement director', 'keywords' => 'directeur mission'],
                ],
            ],
            [
                'slug' => 'consulting-business-dev',
                'name_fr' => 'Business development / Commercial cabinet',
                'name_en' => 'Business Development / Firm Sales',
                'suggestions' => [
                    ['label_fr' => 'Business developer conseil', 'label_en' => 'Consulting business developer', 'keywords' => 'bizdev conseil'],
                    ['label_fr' => 'Responsable développement commercial cabinet', 'label_en' => 'Firm sales development manager', 'keywords' => 'dev commercial cabinet'],
                    ['label_fr' => 'Proposal / bid manager', 'label_en' => 'Proposal / bid manager', 'keywords' => 'proposal bid'],
                    ['label_fr' => 'Key Account Manager services', 'label_en' => 'Services KAM', 'keywords' => 'kam services'],
                ],
            ],
            [
                'slug' => 'consulting-pmo-delivery',
                'name_fr' => 'PMO / Delivery / Chef de projet conseil',
                'name_en' => 'PMO / Delivery / Consulting Project Manager',
                'suggestions' => [
                    ['label_fr' => 'Chef de projet / engagement manager', 'label_en' => 'Project / engagement manager', 'keywords' => 'engagement manager'],
                    ['label_fr' => 'PMO / project management office', 'label_en' => 'PMO', 'keywords' => 'pmo'],
                    ['label_fr' => 'Delivery manager', 'label_en' => 'Delivery manager', 'keywords' => 'delivery manager'],
                    ['label_fr' => 'Scrum master / agile coach (missions)', 'label_en' => 'Scrum master / agile coach', 'keywords' => 'scrum agile'],
                ],
            ],
            [
                'slug' => 'consulting-marketing-comms',
                'name_fr' => 'Marketing / Communication cabinet de conseil',
                'name_en' => 'Consulting Firm Marketing / Comms',
                'suggestions' => [
                    ['label_fr' => 'Responsable marketing cabinet', 'label_en' => 'Firm marketing manager', 'keywords' => 'marketing cabinet'],
                    ['label_fr' => 'Chargé(e) de communication / RP', 'label_en' => 'Communications / PR officer', 'keywords' => 'communication rp'],
                    ['label_fr' => 'Content / thought leadership', 'label_en' => 'Content / thought leadership', 'keywords' => 'thought leadership'],
                    ['label_fr' => 'Community / events manager', 'label_en' => 'Community / events manager', 'keywords' => 'events community'],
                ],
            ],
            [
                'slug' => 'consulting-finance-ops',
                'name_fr' => 'Finance / Contrôle / Ops cabinet',
                'name_en' => 'Firm Finance / Controlling / Ops',
                'suggestions' => [
                    ['label_fr' => 'Contrôleur de gestion cabinet', 'label_en' => 'Firm financial controller', 'keywords' => 'controle gestion'],
                    ['label_fr' => 'Responsable facturation / WIP', 'label_en' => 'Billing / WIP manager', 'keywords' => 'facturation wip'],
                    ['label_fr' => 'Office manager / responsable administratif', 'label_en' => 'Office manager', 'keywords' => 'office manager'],
                    ['label_fr' => 'Credit controller / recouvrement', 'label_en' => 'Credit controller', 'keywords' => 'recouvrement'],
                ],
            ],
            [
                'slug' => 'consulting-admin-support',
                'name_fr' => 'Assistant(e) / Support administratif conseil',
                'name_en' => 'Consulting Admin / Support Assistant',
                'suggestions' => [
                    ['label_fr' => 'Assistant(e) de direction / associé', 'label_en' => 'Executive / partner assistant', 'keywords' => 'assistant direction'],
                    ['label_fr' => 'Assistant(e) de mission / projet', 'label_en' => 'Engagement / project assistant', 'keywords' => 'assistant mission'],
                    ['label_fr' => 'Secrétaire juridique / juridique support', 'label_en' => 'Legal secretary', 'keywords' => 'secretaire juridique'],
                    ['label_fr' => 'Gestionnaire administratif dossiers clients', 'label_en' => 'Client files admin', 'keywords' => 'dossiers clients'],
                ],
            ],
            [
                'slug' => 'consulting-research-analyst',
                'name_fr' => 'Analyste / Research / Knowledge management',
                'name_en' => 'Analyst / Research / Knowledge Management',
                'suggestions' => [
                    ['label_fr' => 'Business analyst / research analyst', 'label_en' => 'Business / research analyst', 'keywords' => 'research analyst'],
                    ['label_fr' => 'Knowledge manager', 'label_en' => 'Knowledge manager', 'keywords' => 'knowledge management'],
                    ['label_fr' => 'Data analyst conseil', 'label_en' => 'Consulting data analyst', 'keywords' => 'data analyst conseil'],
                    ['label_fr' => 'Veille / market intelligence', 'label_en' => 'Market intelligence / watch', 'keywords' => 'veille intelligence'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'marketing-comms',
        'name_fr' => 'Marketing, communication & médias',
        'name_en' => 'Marketing, communications & media',
        'professions' => [
            // ——— Direction & stratégie ———
            [
                'slug' => 'marketing-director',
                'name_fr' => 'Directeur / Responsable marketing',
                'name_en' => 'Marketing Director / Manager',
                'suggestions' => [
                    ['label_fr' => 'Directeur marketing', 'label_en' => 'Marketing director', 'keywords' => 'directeur marketing'],
                    ['label_fr' => 'Responsable marketing', 'label_en' => 'Marketing manager', 'keywords' => 'responsable marketing'],
                    ['label_fr' => 'CMO / Chief Marketing Officer', 'label_en' => 'Chief Marketing Officer', 'keywords' => 'cmo'],
                    ['label_fr' => 'Directeur marketing digital', 'label_en' => 'Digital marketing director', 'keywords' => 'directeur marketing digital'],
                ],
            ],
            [
                'slug' => 'comms-director',
                'name_fr' => 'Directeur / Responsable communication',
                'name_en' => 'Communications Director / Manager',
                'suggestions' => [
                    ['label_fr' => 'Directeur de la communication', 'label_en' => 'Communications director', 'keywords' => 'directeur communication'],
                    ['label_fr' => 'Responsable communication', 'label_en' => 'Communications manager', 'keywords' => 'responsable communication'],
                    ['label_fr' => 'Directeur de la marque / brand director', 'label_en' => 'Brand director', 'keywords' => 'directeur marque'],
                    ['label_fr' => 'Head of corporate communications', 'label_en' => 'Head of corporate communications', 'keywords' => 'corporate communications'],
                ],
            ],
            [
                'slug' => 'media-agency-director',
                'name_fr' => 'Directeur d\'agence / Directeur de clientèle',
                'name_en' => 'Agency Director / Account Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur d\'agence conseil / pub', 'label_en' => 'Agency managing director', 'keywords' => 'directeur agence'],
                    ['label_fr' => 'Directeur de clientèle / account director', 'label_en' => 'Account director', 'keywords' => 'directeur clientele'],
                    ['label_fr' => 'Directeur de création', 'label_en' => 'Creative director', 'keywords' => 'directeur creation'],
                    ['label_fr' => 'Directeur artistique (DA)', 'label_en' => 'Art director', 'keywords' => 'directeur artistique'],
                ],
            ],

            // ——— Marketing ———
            [
                'slug' => 'brand-comms',
                'name_fr' => 'Brand manager / Chef de produit / Marque',
                'name_en' => 'Brand Manager / Product Marketing',
                'suggestions' => [
                    ['label_fr' => 'Brand manager', 'label_en' => 'Brand manager', 'keywords' => 'brand manager marque'],
                    ['label_fr' => 'Chef de produit / product manager marketing', 'label_en' => 'Product marketing manager', 'keywords' => 'chef de produit'],
                    ['label_fr' => 'Junior brand manager / assistant marque', 'label_en' => 'Junior brand / brand assistant', 'keywords' => 'junior brand'],
                    ['label_fr' => 'Category brand / portfolio manager', 'label_en' => 'Category / portfolio brand manager', 'keywords' => 'portfolio marque'],
                    ['label_fr' => 'Chargé(e) de communication', 'label_en' => 'Communications officer', 'keywords' => 'charge communication'],
                ],
            ],
            [
                'slug' => 'digital-marketer',
                'name_fr' => 'Marketing digital / Growth / Performance',
                'name_en' => 'Digital Marketing / Growth / Performance',
                'suggestions' => [
                    ['label_fr' => 'Growth marketer / growth hacker', 'label_en' => 'Growth marketer / hacker', 'keywords' => 'growth'],
                    ['label_fr' => 'Responsable marketing digital', 'label_en' => 'Digital marketing manager', 'keywords' => 'marketing digital'],
                    ['label_fr' => 'Performance marketing manager', 'label_en' => 'Performance marketing manager', 'keywords' => 'performance marketing'],
                    ['label_fr' => 'Traffic manager', 'label_en' => 'Traffic manager', 'keywords' => 'traffic manager'],
                    ['label_fr' => 'CRM / retention marketer', 'label_en' => 'CRM / retention marketer', 'keywords' => 'crm retention'],
                ],
            ],
            [
                'slug' => 'marketing-seo-sea',
                'name_fr' => 'SEO / SEA / SEM / Acquisition payante',
                'name_en' => 'SEO / SEA / SEM / Paid Acquisition',
                'suggestions' => [
                    ['label_fr' => 'Consultant / spécialiste SEO', 'label_en' => 'SEO specialist / consultant', 'keywords' => 'seo referencement'],
                    ['label_fr' => 'Spécialiste SEA / Google Ads', 'label_en' => 'SEA / Google Ads specialist', 'keywords' => 'sea google ads'],
                    ['label_fr' => 'Media buyer / paid social', 'label_en' => 'Media buyer / paid social', 'keywords' => 'media buyer'],
                    ['label_fr' => 'Spécialiste SEM / search', 'label_en' => 'SEM / search specialist', 'keywords' => 'sem search'],
                ],
            ],
            [
                'slug' => 'marketing-social-community',
                'name_fr' => 'Social media / Community management',
                'name_en' => 'Social Media / Community Management',
                'suggestions' => [
                    ['label_fr' => 'Social media manager', 'label_en' => 'Social media manager', 'keywords' => 'social media'],
                    ['label_fr' => 'Community manager', 'label_en' => 'Community manager', 'keywords' => 'community manager'],
                    ['label_fr' => 'Social media strategist', 'label_en' => 'Social media strategist', 'keywords' => 'social strategist'],
                    ['label_fr' => 'Modérateur(trice) / community care', 'label_en' => 'Moderator / community care', 'keywords' => 'moderateur'],
                    ['label_fr' => 'Influence / creator partnerships', 'label_en' => 'Influencer / creator partnerships', 'keywords' => 'influence partnerships'],
                ],
            ],
            [
                'slug' => 'marketing-crm-email',
                'name_fr' => 'CRM / Email / Automation marketing',
                'name_en' => 'CRM / Email / Marketing Automation',
                'suggestions' => [
                    ['label_fr' => 'CRM manager', 'label_en' => 'CRM manager', 'keywords' => 'crm manager'],
                    ['label_fr' => 'Email marketing manager', 'label_en' => 'Email marketing manager', 'keywords' => 'email marketing'],
                    ['label_fr' => 'Marketing automation / HubSpot / Salesforce', 'label_en' => 'Marketing automation specialist', 'keywords' => 'marketing automation'],
                    ['label_fr' => 'Loyalty / programme de fidélité', 'label_en' => 'Loyalty program manager', 'keywords' => 'fidelite loyalty'],
                ],
            ],
            [
                'slug' => 'marketing-product-growth',
                'name_fr' => 'Product marketing / Go-to-market',
                'name_en' => 'Product Marketing / Go-to-Market',
                'suggestions' => [
                    ['label_fr' => 'Product marketing manager', 'label_en' => 'Product marketing manager', 'keywords' => 'product marketing'],
                    ['label_fr' => 'Go-to-market / launch manager', 'label_en' => 'Go-to-market / launch manager', 'keywords' => 'go to market'],
                    ['label_fr' => 'Competitive intelligence marketing', 'label_en' => 'Competitive intelligence', 'keywords' => 'competitive intelligence'],
                    ['label_fr' => 'Enablement / sales marketing', 'label_en' => 'Sales enablement marketing', 'keywords' => 'sales enablement'],
                ],
            ],
            [
                'slug' => 'marketing-trade-events',
                'name_fr' => 'Événementiel / Trade marketing / Activation',
                'name_en' => 'Events / Trade Marketing / Activation',
                'suggestions' => [
                    ['label_fr' => 'Chargé(e) d\'événementiel', 'label_en' => 'Event officer', 'keywords' => 'evenementiel'],
                    ['label_fr' => 'Event manager / chef de projet événement', 'label_en' => 'Event manager', 'keywords' => 'event manager'],
                    ['label_fr' => 'Trade marketing / activation terrain', 'label_en' => 'Trade / field activation', 'keywords' => 'trade activation'],
                    ['label_fr' => 'Responsable salons / stands', 'label_en' => 'Trade shows / booths manager', 'keywords' => 'salons stands'],
                ],
            ],

            // ——— Communication & RP ———
            [
                'slug' => 'comms-pr',
                'name_fr' => 'Relations presse / RP / Affaires publiques',
                'name_en' => 'PR / Press Relations / Public Affairs',
                'suggestions' => [
                    ['label_fr' => 'Attaché(e) de presse / RP', 'label_en' => 'Press officer / PR', 'keywords' => 'attache presse'],
                    ['label_fr' => 'Responsable relations presse', 'label_en' => 'Press relations manager', 'keywords' => 'relations presse'],
                    ['label_fr' => 'Affaires publiques / public affairs', 'label_en' => 'Public affairs', 'keywords' => 'affaires publiques'],
                    ['label_fr' => 'Relations institutionnelles / lobbying', 'label_en' => 'Institutional relations / lobbying', 'keywords' => 'lobbying'],
                ],
            ],
            [
                'slug' => 'comms-internal-corporate',
                'name_fr' => 'Communication interne / Corporate / RSE',
                'name_en' => 'Internal / Corporate / CSR Communications',
                'suggestions' => [
                    ['label_fr' => 'Chargé(e) communication interne', 'label_en' => 'Internal communications officer', 'keywords' => 'communication interne'],
                    ['label_fr' => 'Corporate communications', 'label_en' => 'Corporate communications', 'keywords' => 'corporate'],
                    ['label_fr' => 'Communication RSE / engagement', 'label_en' => 'CSR / engagement communications', 'keywords' => 'communication rse'],
                    ['label_fr' => 'Employer branding / marque employeur', 'label_en' => 'Employer branding', 'keywords' => 'marque employeur'],
                ],
            ],
            [
                'slug' => 'comms-crisis-reputation',
                'name_fr' => 'Communication de crise / E-réputation',
                'name_en' => 'Crisis Comms / E-Reputation',
                'suggestions' => [
                    ['label_fr' => 'Responsable communication de crise', 'label_en' => 'Crisis communications manager', 'keywords' => 'communication crise'],
                    ['label_fr' => 'E-réputation / social listening', 'label_en' => 'E-reputation / social listening', 'keywords' => 'e-reputation'],
                    ['label_fr' => 'Media trainer / porte-parole support', 'label_en' => 'Media trainer / spokesperson support', 'keywords' => 'media training'],
                    ['label_fr' => 'Veille image / brand safety', 'label_en' => 'Brand safety / image monitoring', 'keywords' => 'brand safety'],
                ],
            ],

            // ——— Contenu & création ———
            [
                'slug' => 'content-creator',
                'name_fr' => 'Content manager / Rédacteur / Copywriter',
                'name_en' => 'Content Manager / Writer / Copywriter',
                'suggestions' => [
                    ['label_fr' => 'Content manager', 'label_en' => 'Content manager', 'keywords' => 'content manager'],
                    ['label_fr' => 'Rédacteur(trice) web', 'label_en' => 'Web copywriter', 'keywords' => 'redacteur web'],
                    ['label_fr' => 'Copywriter / concepteur-rédacteur', 'label_en' => 'Copywriter', 'keywords' => 'copywriter'],
                    ['label_fr' => 'Éditorialiste / rédacteur print', 'label_en' => 'Print / editorial writer', 'keywords' => 'redacteur print'],
                    ['label_fr' => 'Chief content officer / head of content', 'label_en' => 'Head of content', 'keywords' => 'head of content'],
                ],
            ],
            [
                'slug' => 'marketing-graphic-design',
                'name_fr' => 'Graphiste / Designer / DA junior',
                'name_en' => 'Graphic Designer / Visual Designer',
                'suggestions' => [
                    ['label_fr' => 'Graphiste print / digital', 'label_en' => 'Print / digital graphic designer', 'keywords' => 'graphiste'],
                    ['label_fr' => 'UI designer / designer digital', 'label_en' => 'UI / digital designer', 'keywords' => 'ui designer'],
                    ['label_fr' => 'Motion designer', 'label_en' => 'Motion designer', 'keywords' => 'motion designer'],
                    ['label_fr' => 'Infographiste / illustrateur(trice)', 'label_en' => 'Infographic designer / illustrator', 'keywords' => 'infographiste'],
                    ['label_fr' => 'Maquettiste / PAO', 'label_en' => 'Layout / DTP designer', 'keywords' => 'maquettiste pao'],
                ],
            ],
            [
                'slug' => 'marketing-video-photo',
                'name_fr' => 'Vidéaste / Photographe / Réalisateur',
                'name_en' => 'Videographer / Photographer / Director',
                'suggestions' => [
                    ['label_fr' => 'Vidéaste / monteur(se)', 'label_en' => 'Videographer / editor', 'keywords' => 'videaste monteur'],
                    ['label_fr' => 'Photographe / retoucheur(se)', 'label_en' => 'Photographer / retoucher', 'keywords' => 'photographe'],
                    ['label_fr' => 'Réalisateur(trice) / director', 'label_en' => 'Director', 'keywords' => 'realisateur'],
                    ['label_fr' => 'Cadreur / opérateur caméra', 'label_en' => 'Camera operator', 'keywords' => 'cadreur'],
                    ['label_fr' => 'Ingénieur du son / sound designer', 'label_en' => 'Sound engineer / designer', 'keywords' => 'son sound'],
                ],
            ],
            [
                'slug' => 'marketing-influencer-creator',
                'name_fr' => 'Créateur de contenu / Influenceur / Talent',
                'name_en' => 'Content Creator / Influencer / Talent',
                'suggestions' => [
                    ['label_fr' => 'Créateur(trice) de contenu', 'label_en' => 'Content creator', 'keywords' => 'createur contenu'],
                    ['label_fr' => 'Influenceur(se) / UGC creator', 'label_en' => 'Influencer / UGC creator', 'keywords' => 'influenceur ugc'],
                    ['label_fr' => 'Talent manager / agent créateurs', 'label_en' => 'Talent manager / creator agent', 'keywords' => 'talent manager'],
                    ['label_fr' => 'Streamer / podcaster', 'label_en' => 'Streamer / podcaster', 'keywords' => 'streamer podcast'],
                ],
            ],

            // ——— Médias & journalisme ———
            [
                'slug' => 'media-journalist',
                'name_fr' => 'Journaliste / Rédacteur en chef / Reporter',
                'name_en' => 'Journalist / Editor / Reporter',
                'suggestions' => [
                    ['label_fr' => 'Journaliste / reporter', 'label_en' => 'Journalist / reporter', 'keywords' => 'journaliste'],
                    ['label_fr' => 'Rédacteur en chef / adjoint', 'label_en' => 'Editor-in-chief / deputy', 'keywords' => 'redacteur en chef'],
                    ['label_fr' => 'Journaliste web / digital news', 'label_en' => 'Digital / web journalist', 'keywords' => 'journaliste web'],
                    ['label_fr' => 'Grand reporter / correspondent', 'label_en' => 'Senior correspondent', 'keywords' => 'grand reporter'],
                    ['label_fr' => 'Secrétaire de rédaction', 'label_en' => 'Copy editor / desk editor', 'keywords' => 'secretaire redaction'],
                ],
            ],
            [
                'slug' => 'media-broadcast',
                'name_fr' => 'Audiovisuel / TV / Radio / Production',
                'name_en' => 'Broadcast / TV / Radio / Production',
                'suggestions' => [
                    ['label_fr' => 'Producteur(trice) audiovisuel', 'label_en' => 'AV producer', 'keywords' => 'producteur audiovisuel'],
                    ['label_fr' => 'Assistant(e) de production', 'label_en' => 'Production assistant', 'keywords' => 'assistant production'],
                    ['label_fr' => 'Présentateur(trice) / animateur radio-TV', 'label_en' => 'TV / radio presenter', 'keywords' => 'presentateur'],
                    ['label_fr' => 'Technicien plateau / régie', 'label_en' => 'Studio / control room tech', 'keywords' => 'regie plateau'],
                    ['label_fr' => 'Chef opérateur / éclairagiste', 'label_en' => 'DoP / lighting', 'keywords' => 'chef operateur'],
                ],
            ],
            [
                'slug' => 'media-planner-buyer',
                'name_fr' => 'Media planner / Acheteur d\'espace / Programmatique',
                'name_en' => 'Media Planner / Media Buyer / Programmatic',
                'suggestions' => [
                    ['label_fr' => 'Media planner', 'label_en' => 'Media planner', 'keywords' => 'media planner'],
                    ['label_fr' => 'Acheteur d\'espace publicitaire', 'label_en' => 'Media space buyer', 'keywords' => 'acheteur espace'],
                    ['label_fr' => 'Trading desk / programmatique', 'label_en' => 'Trading desk / programmatic', 'keywords' => 'programmatique'],
                    ['label_fr' => 'Responsable media / media director', 'label_en' => 'Media director', 'keywords' => 'media director'],
                ],
            ],
            [
                'slug' => 'media-publishing-print',
                'name_fr' => 'Édition / Presse / Publishing',
                'name_en' => 'Publishing / Print Media',
                'suggestions' => [
                    ['label_fr' => 'Éditeur(trice) / editorial manager', 'label_en' => 'Publisher / editorial manager', 'keywords' => 'editeur'],
                    ['label_fr' => 'Chef de fabrication / print production', 'label_en' => 'Print production manager', 'keywords' => 'fabrication print'],
                    ['label_fr' => 'Correcteur(trice) / relecteur(trice)', 'label_en' => 'Proofreader', 'keywords' => 'correcteur'],
                    ['label_fr' => 'Responsable diffusion / abonnements', 'label_en' => 'Circulation / subscriptions manager', 'keywords' => 'diffusion abonnements'],
                ],
            ],

            // ——— Pub, agence, account ———
            [
                'slug' => 'marketing-account-manager',
                'name_fr' => 'Chargé de clientèle / Account manager agence',
                'name_en' => 'Account Manager (Agency)',
                'suggestions' => [
                    ['label_fr' => 'Chargé(e) de clientèle', 'label_en' => 'Account executive', 'keywords' => 'charge clientele'],
                    ['label_fr' => 'Account manager agence', 'label_en' => 'Agency account manager', 'keywords' => 'account manager'],
                    ['label_fr' => 'Chef de publicité', 'label_en' => 'Advertising account lead', 'keywords' => 'chef publicite'],
                    ['label_fr' => 'Planner stratégique / brand strategist', 'label_en' => 'Strategic planner / brand strategist', 'keywords' => 'planner strategique'],
                ],
            ],
            [
                'slug' => 'marketing-advertising-creative',
                'name_fr' => 'Concepteur-rédacteur / Créatif pub',
                'name_en' => 'Creative / Advertising Copy & Concept',
                'suggestions' => [
                    ['label_fr' => 'Concepteur-rédacteur (CR)', 'label_en' => 'Creative copywriter', 'keywords' => 'concepteur redacteur'],
                    ['label_fr' => 'Créatif / creative', 'label_en' => 'Creative', 'keywords' => 'creatif'],
                    ['label_fr' => 'Directeur de création adjoint', 'label_en' => 'Associate creative director', 'keywords' => 'dac'],
                    ['label_fr' => 'UX writer / content designer', 'label_en' => 'UX writer / content designer', 'keywords' => 'ux writer'],
                ],
            ],

            // ——— Data, tech, support ———
            [
                'slug' => 'marketing-data-analytics',
                'name_fr' => 'Data / Analytics / Insights marketing',
                'name_en' => 'Marketing Data / Analytics / Insights',
                'suggestions' => [
                    ['label_fr' => 'Data analyst marketing', 'label_en' => 'Marketing data analyst', 'keywords' => 'data marketing'],
                    ['label_fr' => 'Web analyst / analytics', 'label_en' => 'Web analyst', 'keywords' => 'web analytics'],
                    ['label_fr' => 'Marketing scientist / attribution', 'label_en' => 'Marketing scientist / attribution', 'keywords' => 'attribution'],
                    ['label_fr' => 'Consumer insights / études', 'label_en' => 'Consumer insights / research', 'keywords' => 'consumer insights'],
                ],
            ],
            [
                'slug' => 'marketing-martech',
                'name_fr' => 'MarTech / Chef de projet digital / CMS',
                'name_en' => 'MarTech / Digital Project / CMS',
                'suggestions' => [
                    ['label_fr' => 'Chef de projet digital', 'label_en' => 'Digital project manager', 'keywords' => 'chef projet digital'],
                    ['label_fr' => 'MarTech / stack marketing', 'label_en' => 'MarTech specialist', 'keywords' => 'martech'],
                    ['label_fr' => 'Webmaster / administrateur CMS', 'label_en' => 'Webmaster / CMS admin', 'keywords' => 'webmaster cms'],
                    ['label_fr' => 'Intégrateur web / email', 'label_en' => 'Web / email integrator', 'keywords' => 'integrateur'],
                ],
            ],
            [
                'slug' => 'marketing-research-studies',
                'name_fr' => 'Études / Market research / Sondages',
                'name_en' => 'Market Research / Surveys',
                'suggestions' => [
                    ['label_fr' => 'Chargé(e) d\'études marketing', 'label_en' => 'Market research officer', 'keywords' => 'etudes marketing'],
                    ['label_fr' => 'Responsable études / insights', 'label_en' => 'Research / insights manager', 'keywords' => 'insights'],
                    ['label_fr' => 'Enquêteur(trice) / terrain sondages', 'label_en' => 'Survey field interviewer', 'keywords' => 'enqueteur'],
                    ['label_fr' => 'Statisticien(ne) / data études', 'label_en' => 'Research statistician', 'keywords' => 'statisticien etudes'],
                ],
            ],
            [
                'slug' => 'marketing-sales-bizdev',
                'name_fr' => 'Commercial agence / New business',
                'name_en' => 'Agency Sales / New Business',
                'suggestions' => [
                    ['label_fr' => 'New business / développement commercial', 'label_en' => 'New business development', 'keywords' => 'new business'],
                    ['label_fr' => 'Commercial agence conseil / média', 'label_en' => 'Agency sales', 'keywords' => 'commercial agence'],
                    ['label_fr' => 'Responsable partenariats / sponsoring', 'label_en' => 'Partnerships / sponsorship manager', 'keywords' => 'sponsoring'],
                    ['label_fr' => 'Media sales / régie publicitaire', 'label_en' => 'Media / ad sales', 'keywords' => 'regie publicitaire'],
                ],
            ],
            [
                'slug' => 'marketing-hr-admin',
                'name_fr' => 'RH / Production / Support agence & médias',
                'name_en' => 'HR / Traffic / Agency & Media Support',
                'suggestions' => [
                    ['label_fr' => 'Traffic manager production / planning créa', 'label_en' => 'Production traffic manager', 'keywords' => 'traffic production'],
                    ['label_fr' => 'Assistant(e) commercial(e) / account', 'label_en' => 'Account / sales assistant', 'keywords' => 'assistant account'],
                    ['label_fr' => 'Office manager / admin agence', 'label_en' => 'Agency office manager', 'keywords' => 'office manager'],
                    ['label_fr' => 'Chargé production print / livrables', 'label_en' => 'Print / deliverables production', 'keywords' => 'production print'],
                    ['label_fr' => 'RH / talent agence créative', 'label_en' => 'Creative agency HR / talent', 'keywords' => 'rh agence'],
                ],
            ],
        ],
    ],
    [
        'slug' => 'education',
        'name_fr' => 'Éducation & formation',
        'name_en' => 'Education & training',
        'professions' => [
            // ——— Direction & encadrement ———
            [
                'slug' => 'education-school-director',
                'name_fr' => 'Directeur / Chef d\'établissement',
                'name_en' => 'School / Institution Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur d\'école / établissement', 'label_en' => 'School / institution director', 'keywords' => 'directeur etablissement'],
                    ['label_fr' => 'Proviseur / principal', 'label_en' => 'High school / middle school principal', 'keywords' => 'proviseur principal'],
                    ['label_fr' => 'Directeur adjoint / CPE', 'label_en' => 'Deputy head / student life', 'keywords' => 'directeur adjoint cpe'],
                    ['label_fr' => 'Directeur d\'université / doyen', 'label_en' => 'University director / dean', 'keywords' => 'doyen universite'],
                ],
            ],
            [
                'slug' => 'education-training-center-manager',
                'name_fr' => 'Responsable centre / organisme de formation',
                'name_en' => 'Training Center / Provider Manager',
                'suggestions' => [
                    ['label_fr' => 'Responsable centre de formation', 'label_en' => 'Training center manager', 'keywords' => 'centre formation'],
                    ['label_fr' => 'Directeur organisme de formation (OF)', 'label_en' => 'Training provider director', 'keywords' => 'organisme formation'],
                    ['label_fr' => 'Responsable CFA / apprentissage', 'label_en' => 'Apprenticeship center manager', 'keywords' => 'cfa apprentissage'],
                    ['label_fr' => 'Responsable campus / site pédagogique', 'label_en' => 'Campus / training site manager', 'keywords' => 'campus pedagogique'],
                ],
            ],
            [
                'slug' => 'education-pedagogical-director',
                'name_fr' => 'Directeur pédagogique / Responsable pédagogique',
                'name_en' => 'Pedagogical / Academic Director',
                'suggestions' => [
                    ['label_fr' => 'Directeur pédagogique', 'label_en' => 'Pedagogical director', 'keywords' => 'directeur pedagogique'],
                    ['label_fr' => 'Responsable pédagogique', 'label_en' => 'Academic / pedagogical manager', 'keywords' => 'responsable pedagogique'],
                    ['label_fr' => 'Coordinateur(trice) pédagogique', 'label_en' => 'Pedagogical coordinator', 'keywords' => 'coordinateur pedagogique'],
                    ['label_fr' => 'Responsable qualité formation / Qualiopi', 'label_en' => 'Training quality / Qualiopi manager', 'keywords' => 'qualiopi qualite'],
                ],
            ],

            // ——— Enseignement scolaire ———
            [
                'slug' => 'teacher',
                'name_fr' => 'Enseignant(e) / Professeur',
                'name_en' => 'Teacher / Professor',
                'suggestions' => [
                    ['label_fr' => 'Professeur des écoles / instituteur(trice)', 'label_en' => 'Primary school teacher', 'keywords' => 'primaire instituteur'],
                    ['label_fr' => 'Enseignant(e) collège / lycée', 'label_en' => 'Secondary school teacher', 'keywords' => 'college lycee'],
                    ['label_fr' => 'Professeur de lycée professionnel', 'label_en' => 'Vocational high school teacher', 'keywords' => 'lycee professionnel'],
                    ['label_fr' => 'Enseignant(e) spécialisé(e) / ASH', 'label_en' => 'Special needs teacher', 'keywords' => 'ash specialise'],
                    ['label_fr' => 'Professeur documentaliste', 'label_en' => 'Teacher-librarian', 'keywords' => 'documentaliste'],
                ],
            ],
            [
                'slug' => 'education-subject-teacher',
                'name_fr' => 'Enseignant disciplinaire / Matières',
                'name_en' => 'Subject Teacher',
                'suggestions' => [
                    ['label_fr' => 'Mathématiques / sciences', 'label_en' => 'Maths / sciences', 'keywords' => 'maths sciences'],
                    ['label_fr' => 'Lettres / langues', 'label_en' => 'Languages / literature', 'keywords' => 'lettres langues'],
                    ['label_fr' => 'Histoire-géographie / SES', 'label_en' => 'History-geography / social sciences', 'keywords' => 'histoire geo ses'],
                    ['label_fr' => 'EPS / arts / musique', 'label_en' => 'PE / arts / music', 'keywords' => 'eps arts musique'],
                    ['label_fr' => 'Technologie / informatique scolaire', 'label_en' => 'Technology / school IT', 'keywords' => 'technologie informatique'],
                ],
            ],
            [
                'slug' => 'education-early-childhood',
                'name_fr' => 'Petite enfance / Préscolaire / Crèche',
                'name_en' => 'Early Childhood / Preschool / Nursery',
                'suggestions' => [
                    ['label_fr' => 'Éducateur(trice) de jeunes enfants (EJE)', 'label_en' => 'Early childhood educator', 'keywords' => 'eje petite enfance'],
                    ['label_fr' => 'Auxiliaire de puériculture', 'label_en' => 'Childcare assistant', 'keywords' => 'auxiliaire puericulture'],
                    ['label_fr' => 'ATSEM / agent maternelle', 'label_en' => 'Classroom / nursery assistant', 'keywords' => 'atsem'],
                    ['label_fr' => 'Directeur(trice) de crèche / EAJE', 'label_en' => 'Nursery / daycare director', 'keywords' => 'directeur creche'],
                    ['label_fr' => 'Assistant(e) maternel(le)', 'label_en' => 'Childminder', 'keywords' => 'assistant maternel'],
                ],
            ],
            [
                'slug' => 'education-special-needs',
                'name_fr' => 'AESH / Inclusion / Handicap',
                'name_en' => 'Learning Support / Inclusion / Disability',
                'suggestions' => [
                    ['label_fr' => 'AESH / accompagnant élèves en situation de handicap', 'label_en' => 'Teaching assistant (SEND)', 'keywords' => 'aesh handicap'],
                    ['label_fr' => 'Éducateur(trice) spécialisé(e)', 'label_en' => 'Specialized educator', 'keywords' => 'educateur specialise'],
                    ['label_fr' => 'Référent(e) inclusion / ULIS', 'label_en' => 'Inclusion / ULIS coordinator', 'keywords' => 'inclusion ulis'],
                    ['label_fr' => 'Orthopédagogue / remédiation', 'label_en' => 'Remedial education specialist', 'keywords' => 'orthopedagogue'],
                ],
            ],

            // ——— Enseignement supérieur ———
            [
                'slug' => 'education-higher-lecturer',
                'name_fr' => 'Enseignant-chercheur / Maître de conférences',
                'name_en' => 'Lecturer / Academic / Researcher',
                'suggestions' => [
                    ['label_fr' => 'Maître de conférences / MCF', 'label_en' => 'Associate professor / lecturer', 'keywords' => 'maitre conferences'],
                    ['label_fr' => 'Professeur des universités', 'label_en' => 'Full professor', 'keywords' => 'professeur universite'],
                    ['label_fr' => 'Enseignant-chercheur', 'label_en' => 'Teacher-researcher', 'keywords' => 'enseignant chercheur'],
                    ['label_fr' => 'ATER / vacataire / chargé de cours', 'label_en' => 'Teaching assistant / adjunct', 'keywords' => 'ater vacataire'],
                    ['label_fr' => 'Chercheur(se) / post-doc', 'label_en' => 'Researcher / postdoc', 'keywords' => 'chercheur postdoc'],
                ],
            ],
            [
                'slug' => 'education-higher-admin',
                'name_fr' => 'Administration enseignement supérieur / Scolarité',
                'name_en' => 'Higher Education Admin / Registrar',
                'suggestions' => [
                    ['label_fr' => 'Responsable scolarité / admissions', 'label_en' => 'Registrar / admissions manager', 'keywords' => 'scolarite admissions'],
                    ['label_fr' => 'Gestionnaire pédagogique / planning', 'label_en' => 'Academic admin / scheduling', 'keywords' => 'gestionnaire pedagogique'],
                    ['label_fr' => 'Responsable relations internationales', 'label_en' => 'International relations manager', 'keywords' => 'relations internationales'],
                    ['label_fr' => 'Chargé vie étudiante / campus life', 'label_en' => 'Student life officer', 'keywords' => 'vie etudiante'],
                ],
            ],

            // ——— Formation professionnelle ———
            [
                'slug' => 'education-corporate-trainer',
                'name_fr' => 'Formateur professionnel / Formateur entreprise',
                'name_en' => 'Corporate / Professional Trainer',
                'suggestions' => [
                    ['label_fr' => 'Formateur(trice) professionnel(le)', 'label_en' => 'Professional trainer', 'keywords' => 'formateur professionnel'],
                    ['label_fr' => 'Formateur(trice) interne entreprise', 'label_en' => 'In-house corporate trainer', 'keywords' => 'formateur interne'],
                    ['label_fr' => 'Formateur(trice) technique / métier', 'label_en' => 'Technical / trade trainer', 'keywords' => 'formateur technique'],
                    ['label_fr' => 'Formateur(trice) soft skills / management', 'label_en' => 'Soft skills / management trainer', 'keywords' => 'soft skills'],
                    ['label_fr' => 'Formateur(trice) FLE / langues', 'label_en' => 'Language / FLE trainer', 'keywords' => 'fle langues'],
                ],
            ],
            [
                'slug' => 'edtech',
                'name_fr' => 'Ingénieur pédagogique / Instructional designer',
                'name_en' => 'Instructional Designer / Learning Engineer',
                'suggestions' => [
                    ['label_fr' => 'Ingénieur pédagogique', 'label_en' => 'Instructional designer', 'keywords' => 'ingenieur pedagogique'],
                    ['label_fr' => 'Concepteur(trice) e-learning / digital learning', 'label_en' => 'E-learning / digital learning designer', 'keywords' => 'elearning'],
                    ['label_fr' => 'Learning experience designer (LXD)', 'label_en' => 'Learning experience designer', 'keywords' => 'lxd'],
                    ['label_fr' => 'Scénariste pédagogique / storyboard', 'label_en' => 'Learning storyboard writer', 'keywords' => 'storyboard pedagogique'],
                    ['label_fr' => 'Intégrateur LMS / SCORM / xAPI', 'label_en' => 'LMS / SCORM / xAPI integrator', 'keywords' => 'lms scorm'],
                ],
            ],
            [
                'slug' => 'education-ld-manager',
                'name_fr' => 'Responsable formation / L&D / Academy',
                'name_en' => 'L&D / Training / Academy Manager',
                'suggestions' => [
                    ['label_fr' => 'Responsable formation / L&D', 'label_en' => 'L&D / training manager', 'keywords' => 'responsable formation'],
                    ['label_fr' => 'Learning & Development manager', 'label_en' => 'Learning & Development manager', 'keywords' => 'learning development'],
                    ['label_fr' => 'Responsable academy / université d\'entreprise', 'label_en' => 'Corporate academy manager', 'keywords' => 'academy entreprise'],
                    ['label_fr' => 'Chargé(e) ingénierie de formation', 'label_en' => 'Training engineering officer', 'keywords' => 'ingenierie formation'],
                ],
            ],
            [
                'slug' => 'education-apprenticeship',
                'name_fr' => 'Apprentissage / Alternance / Tutorat',
                'name_en' => 'Apprenticeship / Work-Study / Tutoring',
                'suggestions' => [
                    ['label_fr' => 'Maître d\'apprentissage / tuteur entreprise', 'label_en' => 'Apprenticeship master / company tutor', 'keywords' => 'maitre apprentissage'],
                    ['label_fr' => 'Responsable alternance / relations entreprises', 'label_en' => 'Work-study / employer relations', 'keywords' => 'alternance'],
                    ['label_fr' => 'Chargé suivi apprentis', 'label_en' => 'Apprentice follow-up officer', 'keywords' => 'suivi apprentis'],
                    ['label_fr' => 'Conseiller apprentissage / CFA', 'label_en' => 'Apprenticeship advisor', 'keywords' => 'conseiller apprentissage'],
                ],
            ],
            [
                'slug' => 'education-career-guidance',
                'name_fr' => 'Orientation / Conseil en évolution professionnelle',
                'name_en' => 'Career Guidance / Professional Development Advisory',
                'suggestions' => [
                    ['label_fr' => 'Conseiller(ère) d\'orientation', 'label_en' => 'Career guidance counselor', 'keywords' => 'orientation'],
                    ['label_fr' => 'Conseiller CEP / bilan de compétences', 'label_en' => 'Career counseling / skills assessment', 'keywords' => 'cep bilan'],
                    ['label_fr' => 'Conseiller VAE / validation des acquis', 'label_en' => 'Prior learning (VAE) advisor', 'keywords' => 'vae'],
                    ['label_fr' => 'Coach carrière / employabilité', 'label_en' => 'Career / employability coach', 'keywords' => 'coach carriere'],
                ],
            ],

            // ——— Langues, soutien, parascolaire ———
            [
                'slug' => 'education-language-teacher',
                'name_fr' => 'Professeur de langues / FLE / Examens',
                'name_en' => 'Language Teacher / FLE / Exam Prep',
                'suggestions' => [
                    ['label_fr' => 'Professeur de langues', 'label_en' => 'Language teacher', 'keywords' => 'professeur langues'],
                    ['label_fr' => 'Enseignant FLE / FLS', 'label_en' => 'FLE / FLS teacher', 'keywords' => 'fle fls'],
                    ['label_fr' => 'Préparation examens (TOEIC, IELTS…)', 'label_en' => 'Exam prep (TOEIC, IELTS…)', 'keywords' => 'toeic ielts'],
                    ['label_fr' => 'Intervenant centre de langues', 'label_en' => 'Language center instructor', 'keywords' => 'centre langues'],
                ],
            ],
            [
                'slug' => 'education-tutoring',
                'name_fr' => 'Soutien scolaire / Tutorat / Coaching scolaire',
                'name_en' => 'Tutoring / Academic Coaching',
                'suggestions' => [
                    ['label_fr' => 'Tuteur(trice) / professeur particulier', 'label_en' => 'Private tutor', 'keywords' => 'cours particuliers'],
                    ['label_fr' => 'Coach scolaire / méthodologie', 'label_en' => 'Academic / study coach', 'keywords' => 'coach scolaire'],
                    ['label_fr' => 'Animateur(trice) aide aux devoirs', 'label_en' => 'Homework help facilitator', 'keywords' => 'aide devoirs'],
                    ['label_fr' => 'Responsable centre de soutien', 'label_en' => 'Tutoring center manager', 'keywords' => 'centre soutien'],
                ],
            ],
            [
                'slug' => 'education-extracurricular',
                'name_fr' => 'Animation / Périscolaire / Éducation populaire',
                'name_en' => 'After-School / Youth Work / Popular Education',
                'suggestions' => [
                    ['label_fr' => 'Animateur(trice) périscolaire', 'label_en' => 'After-school animator', 'keywords' => 'periscolaire'],
                    ['label_fr' => 'Directeur(trice) d\'accueil de loisirs', 'label_en' => 'Leisure center director', 'keywords' => 'accueil loisirs'],
                    ['label_fr' => 'Éducateur(trice) sportif / BAFA-BASS', 'label_en' => 'Sports / youth educator', 'keywords' => 'bafa educateur'],
                    ['label_fr' => 'Animateur(trice) socioculturel(le)', 'label_en' => 'Sociocultural animator', 'keywords' => 'animation socioculturelle'],
                ],
            ],

            // ——— EdTech & digital ———
            [
                'slug' => 'education-edtech-product',
                'name_fr' => 'EdTech / Product / Digital education',
                'name_en' => 'EdTech / Product / Digital Education',
                'suggestions' => [
                    ['label_fr' => 'Product manager EdTech', 'label_en' => 'EdTech product manager', 'keywords' => 'product edtech'],
                    ['label_fr' => 'Responsable digital learning', 'label_en' => 'Digital learning manager', 'keywords' => 'digital learning'],
                    ['label_fr' => 'Administrateur LMS / plateforme', 'label_en' => 'LMS / platform admin', 'keywords' => 'administrateur lms'],
                    ['label_fr' => 'Customer success / onboarding EdTech', 'label_en' => 'EdTech customer success', 'keywords' => 'customer success edtech'],
                ],
            ],
            [
                'slug' => 'education-exam-assessment',
                'name_fr' => 'Évaluation / Examens / Certification',
                'name_en' => 'Assessment / Exams / Certification',
                'suggestions' => [
                    ['label_fr' => 'Responsable examens / certification', 'label_en' => 'Exams / certification manager', 'keywords' => 'examens certification'],
                    ['label_fr' => 'Concepteur(trice) d\'épreuves / items', 'label_en' => 'Test / item writer', 'keywords' => 'conception epreuves'],
                    ['label_fr' => 'Surveillant(e) / organisateur examens', 'label_en' => 'Exam invigilator / organizer', 'keywords' => 'surveillant examens'],
                    ['label_fr' => 'Jury / évaluateur(trice)', 'label_en' => 'Assessor / jury member', 'keywords' => 'evaluateur jury'],
                ],
            ],

            // ——— Vie scolaire & support élève ———
            [
                'slug' => 'education-student-life',
                'name_fr' => 'Vie scolaire / CPE / Surveillance',
                'name_en' => 'Student Life / Pastoral / Supervision',
                'suggestions' => [
                    ['label_fr' => 'Conseiller principal d\'éducation (CPE)', 'label_en' => 'Pastoral / student life counselor', 'keywords' => 'cpe'],
                    ['label_fr' => 'Assistant(e) d\'éducation / surveillant(e)', 'label_en' => 'Teaching assistant / supervisor', 'keywords' => 'assistant education'],
                    ['label_fr' => 'Responsable vie scolaire', 'label_en' => 'Student life manager', 'keywords' => 'vie scolaire'],
                    ['label_fr' => 'Médiateur(trice) scolaire', 'label_en' => 'School mediator', 'keywords' => 'mediateur scolaire'],
                ],
            ],
            [
                'slug' => 'education-library-docs',
                'name_fr' => 'Documentation / CDI / Bibliothèque',
                'name_en' => 'Library / Learning Resources',
                'suggestions' => [
                    ['label_fr' => 'Documentaliste / CDI', 'label_en' => 'School librarian / CDI', 'keywords' => 'cdi documentaliste'],
                    ['label_fr' => 'Bibliothécaire', 'label_en' => 'Librarian', 'keywords' => 'bibliothecaire'],
                    ['label_fr' => 'Gestionnaire ressources pédagogiques', 'label_en' => 'Learning resources officer', 'keywords' => 'ressources pedagogiques'],
                    ['label_fr' => 'Archiviste / centre de documentation', 'label_en' => 'Archivist / documentation center', 'keywords' => 'archiviste'],
                ],
            ],
            [
                'slug' => 'education-psychosocial',
                'name_fr' => 'Psychologue scolaire / Social / Santé scolaire',
                'name_en' => 'School Psychologist / Social / Health',
                'suggestions' => [
                    ['label_fr' => 'Psychologue de l\'éducation / scolaire', 'label_en' => 'School / education psychologist', 'keywords' => 'psychologue scolaire'],
                    ['label_fr' => 'Assistant(e) social(e) scolaire', 'label_en' => 'School social worker', 'keywords' => 'assistant social scolaire'],
                    ['label_fr' => 'Infirmier(ère) scolaire', 'label_en' => 'School nurse', 'keywords' => 'infirmier scolaire'],
                    ['label_fr' => 'Médecin scolaire', 'label_en' => 'School doctor', 'keywords' => 'medecin scolaire'],
                ],
            ],

            // ——— Commercial, admin, support ———
            [
                'slug' => 'education-sales-admissions',
                'name_fr' => 'Commercial formation / Admissions / Recrutement élèves',
                'name_en' => 'Training Sales / Admissions / Student Recruitment',
                'suggestions' => [
                    ['label_fr' => 'Commercial formation B2B / B2C', 'label_en' => 'Training sales B2B / B2C', 'keywords' => 'commercial formation'],
                    ['label_fr' => 'Chargé admissions / recrutement étudiants', 'label_en' => 'Admissions / student recruitment', 'keywords' => 'admissions'],
                    ['label_fr' => 'Conseiller(ère) formation / CPF', 'label_en' => 'Training / CPF advisor', 'keywords' => 'conseiller cpf'],
                    ['label_fr' => 'Responsable partenariats entreprises / écoles', 'label_en' => 'School / employer partnerships', 'keywords' => 'partenariats ecoles'],
                ],
            ],
            [
                'slug' => 'education-admin-finance',
                'name_fr' => 'Administration / Gestion / Finance établissement',
                'name_en' => 'School Admin / Finance / Operations',
                'suggestions' => [
                    ['label_fr' => 'Gestionnaire administratif établissement', 'label_en' => 'School admin officer', 'keywords' => 'gestionnaire administratif'],
                    ['label_fr' => 'Intendant(e) / gestionnaire financier', 'label_en' => 'Bursar / financial manager', 'keywords' => 'intendant'],
                    ['label_fr' => 'Secrétaire de direction / scolarité', 'label_en' => 'Registrar / executive secretary', 'keywords' => 'secretaire scolarite'],
                    ['label_fr' => 'Comptable établissement / OF', 'label_en' => 'School / training provider accountant', 'keywords' => 'comptable etablissement'],
                ],
            ],
            [
                'slug' => 'education-it-support',
                'name_fr' => 'SI éducatif / Support numérique / ENT',
                'name_en' => 'Education IT / Digital Support / LMS Ops',
                'suggestions' => [
                    ['label_fr' => 'Administrateur ENT / SI scolaire', 'label_en' => 'School IT / portal admin', 'keywords' => 'ent si scolaire'],
                    ['label_fr' => 'Technicien support numérique éducatif', 'label_en' => 'Education IT support tech', 'keywords' => 'support numerique'],
                    ['label_fr' => 'Référent numérique / digital coach', 'label_en' => 'Digital referent / coach', 'keywords' => 'referent numerique'],
                    ['label_fr' => 'Chef de projet SI éducation', 'label_en' => 'Education IT project manager', 'keywords' => 'projet si education'],
                ],
            ],
            [
                'slug' => 'education-facilities',
                'name_fr' => 'Vie de campus / Logistique / Internat',
                'name_en' => 'Campus Life / Logistics / Boarding',
                'suggestions' => [
                    ['label_fr' => 'Surveillant(e) d\'internat / éducateur internat', 'label_en' => 'Boarding supervisor', 'keywords' => 'internat'],
                    ['label_fr' => 'Responsable logistique / moyens généraux école', 'label_en' => 'School facilities / logistics', 'keywords' => 'moyens generaux ecole'],
                    ['label_fr' => 'Agent d\'accueil établissement', 'label_en' => 'School reception officer', 'keywords' => 'accueil etablissement'],
                    ['label_fr' => 'Responsable restauration scolaire', 'label_en' => 'School catering manager', 'keywords' => 'restauration scolaire'],
                ],
            ],
            [
                'slug' => 'education-hr',
                'name_fr' => 'RH / Formation des formateurs',
                'name_en' => 'HR / Train-the-Trainer',
                'suggestions' => [
                    ['label_fr' => 'Responsable RH établissement / OF', 'label_en' => 'School / training HR manager', 'keywords' => 'rh etablissement'],
                    ['label_fr' => 'Formateur de formateurs', 'label_en' => 'Train-the-trainer', 'keywords' => 'formateur de formateurs'],
                    ['label_fr' => 'Chargé recrutement enseignants / formateurs', 'label_en' => 'Teacher / trainer recruiter', 'keywords' => 'recrutement enseignants'],
                    ['label_fr' => 'Gestionnaire planning intervenants', 'label_en' => 'Trainer schedule planner', 'keywords' => 'planning intervenants'],
                ],
            ],
        ],
    ],
];
