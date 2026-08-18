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
                    ['label_fr' => 'Chargé(e) de candidatures', 'label_en' => 'Talent acquisition specialist', 'keywords' => 'sourcing talent acquisition'],
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
