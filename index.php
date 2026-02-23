<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Page d'accueil publique du système.
// Explique comment utiliser le bot Telegram et donne accès à l'administration.

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guichet Administratif Sud-Kivu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 40px 0; 
        }
        .card { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            margin-bottom: 30px;
        }
        .hero { 
            text-align: center; 
            color: white; 
            padding: 40px 0; 
        }
        .feature-box { 
            padding: 20px; 
            border-left: 4px solid #667eea; 
            margin-bottom: 15px; 
            background-color: #f8f9fa; 
            border-radius: 5px; 
        }
        .btn-telegram { 
            background: #0088cc; 
            border: none; 
            padding: 15px 40px; 
            font-size: 1.1rem; 
        }
        .btn-telegram:hover { 
            background: #006699; 
            transform: translateY(-2px); 
            transition: 0.3s; 
        }
        .step-number { 
            display: inline-block; 
            width: 35px; 
            height: 35px; 
            background: #667eea; 
            color: white; 
            border-radius: 50%; 
            line-height: 35px; 
            text-align: center; 
            font-weight: bold; 
            margin-right: 10px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HERO SECTION -->
        <div class="hero">
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-building mb-3" viewBox="0 0 16 16">
                <path d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1ZM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z"/>
                <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V1Zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3V1Z"/>
            </svg>
            <h1 class="display-4 fw-bold">Guichet Administratif Sud-Kivu</h1>
            <p class="lead">Obtenez vos documents officiels facilement via Telegram</p>
        </div>

        <!-- DESCRIPTION DU SERVICE -->
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body p-5">
                        <h3 class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-info-circle text-primary me-2" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                            </svg>
                            Qu'est-ce que ce service ?
                        </h3>
                        <p class="fs-5">
                            Notre bot Telegram vous permet de demander et recevoir vos documents administratifs officiels en quelques minutes, 
                            sans vous déplacer. Simple, rapide et sécurisé.
                        </p>

                        <h4 class="mt-5 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-text text-success me-2" viewBox="0 0 16 16">
                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                            </svg>
                            Documents disponibles
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="text-success fw-bold">•</span> Attestation de résidence</li>
                                    <li class="mb-2"><span class="text-success fw-bold">•</span> Certificat de scolarité</li>
                                    <li class="mb-2"><span class="text-success fw-bold">•</span> Attestation de naissance</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><span class="text-success fw-bold">•</span> Attestation de bonne vie et moeurs</li>
                                    <li class="mb-2"><span class="text-success fw-bold">•</span> Certificat de célibat</li>
                                </ul>
                            </div>
                        </div>

                        <h4 class="mt-5 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-list-ol text-warning me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
                                <path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z"/>
                            </svg>
                            Comment ça marche ?
                        </h4>

                        <div class="feature-box">
                            <strong><span class="step-number">1</span> Vérifiez votre enregistrement</strong>
                            <p class="mb-0 mt-2">Votre numéro de pièce d'identité doit être enregistré dans notre système. 
                            Si ce n'est pas le cas, contactez l'administration.</p>
                        </div>

                        <div class="feature-box">
                            <strong><span class="step-number">2</span> Ouvrez Telegram</strong>
                            <p class="mb-0 mt-2">Cliquez sur le bouton ci-dessous pour accéder directement à notre bot sur Telegram.</p>
                        </div>

                        <div class="feature-box">
                            <strong><span class="step-number">3</span> Démarrez la conversation</strong>
                            <p class="mb-0 mt-2">Envoyez <code>/start</code> au bot et suivez les instructions. Vous devrez fournir votre numéro de pièce d'identité.</p>
                        </div>

                        <div class="feature-box">
                            <strong><span class="step-number">4</span> Choisissez votre document</strong>
                            <p class="mb-0 mt-2">Sélectionnez le type de document dont vous avez besoin dans la liste proposée.</p>
                        </div>

                        <div class="feature-box">
                            <strong><span class="step-number">5</span> Recevez votre document</strong>
                            <p class="mb-0 mt-2">Le bot génère votre document en format PDF et vous envoie un lien de téléchargement valable 15 minutes.</p>
                        </div>

                        <div class="alert alert-warning mt-4" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle me-2" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/>
                            </svg>
                            <strong>Important :</strong> Votre numéro de pièce d'identité doit être enregistré dans notre base de données. 
                            Si vous n'êtes pas enregistré, le bot ne pourra pas générer vos documents.
                        </div>

                        <div class="text-center mt-5">
                            <a href="https://t.me/winasrdc_bot" target="_blank" class="btn btn-telegram btn-lg text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-telegram me-2" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.287 5.906c-.778.324-2.334.994-4.666 2.01-.378.15-.577.298-.595.442-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294.26.006.549-.1.868-.32 2.179-1.471 3.304-2.214 3.374-2.23.05-.012.12-.026.166.016.047.041.042.12.037.141-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8.154 8.154 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629.093.06.183.125.27.187.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.426 1.426 0 0 0-.013-.315.337.337 0 0 0-.114-.217.526.526 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09z"/>
                                </svg>
                                Commencer à chater pour votre document
                            </a>
                        </div>
                    </div>
                </div>

                <!-- SECTION ADMINISTRATION -->
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <h5 class="mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-shield-lock text-primary me-2" viewBox="0 0 16 16">
                                <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
                                <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415z"/>
                            </svg>
                            Vous êtes administrateur ?
                        </h5>
                        <p class="text-muted">Accédez au panneau d'administration pour gérer les citoyens et les demandes</p>
                        <a href="connexion_admin.php" class="btn btn-outline-primary">Se connecter à l'administration</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
