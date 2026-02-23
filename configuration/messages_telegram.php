<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Messages du bot Telegram centralisés pour faciliter la modification.

return [
    // Message de bienvenue
    'bienvenue' => "Bienvenue à Goverment Services Portal\n\n" .
                   "Je suis Asnath Kajibwami, coordinateur du service.\n\n" .
                   "Ce portail vous offre un accès rapide et sécurisé aux documents administratifs de Sud-Kivu.\n\n" .
                   "Pour commencer, envoyez votre numéro de pièce d'identité.",
    
    // Demande de pièce
    'demande_piece' => "Veuillez envoyer votre numéro de pièce d'identité.\n\n" .
                       "Exemple: OP-14862992 ou 33644907501",
    
    // Erreur pièce introuvable
    'piece_introuvable' => "Désolé, ce numéro n'est pas enregistré dans notre système.\n\n" .
                           "Veuillez vérifier votre numéro d'identité et réessayer.\n\n" .
                           "Tapez /start pour recommencer.",
    
    // Identité validée
    'identite_validee' => "✓ Identité validée\n\n" .
                          "Nom: %s\n" .
                          "Numéro: %s\n\n" .
                          "Choisissez le document dont vous avez besoin:",
    
    // Document non reconnu
    'document_non_reconnu' => "Document non reconnu.\n\n" .
                              "Veuillez choisir un document dans la liste proposée.",
    
    // Session invalide
    'session_invalide' => "Votre session a expiré.\n\n" .
                          "Tapez /start pour recommencer.",
    
    // Document généré
    'document_genere' => "✓ Votre document a été généré avec succès\n\n" .
                         "Document: %s\n" .
                         "Lien de téléchargement: %s\n\n" .
                         "⚠ IMPORTANT:\n" .
                         "- Ce lien expire dans %d minutes\n" .
                         "- Le document sera supprimé après téléchargement\n" .
                         "- Téléchargez-le maintenant\n\n" .
                         "Date d'expiration: %s",
    
    // Erreur générale
    'erreur_generale' => "Une erreur est survenue.\n\n" .
                         "Veuillez réessayer ou contactez l'aide.",
    
    // Commande inconnue
    'commande_inconnue' => "Commande non reconnue.\n\n" .
                           "Tapez /start pour commencer.",
    
    // Aide
    'aide' => "🏛️ GOVERMENT SERVICES PORTAL - SUD-KIVU\n\n" .
              "Service administratif en ligne\n" .
              "Coordonnateur: Asnath Kajibwami\n" .
              "Contact: 0983414279\n\n" .
              "📋 Comment utiliser ce bot:\n\n" .
              "1. Tapez /start\n" .
              "2. Envoyez votre numéro de pièce d'identité\n" .
              "3. Choisissez le document souhaité\n" .
              "4. Téléchargez votre document\n\n" .
              "📄 Documents disponibles:\n" .
              "- Attestation de résidence\n" .
              "- Certificat de scolarité\n" .
              "- Attestation de naissance\n" .
              "- Attestation de bonne vie et moeurs\n" .
              "- Certificat de célibat\n\n" .
              "Pour toute question ou assistance:\n" .
              "Appelez Asnath Kajibwami au 0983414279\n\n" .
              "✓ Service sécurisé et confidentiel",
];
