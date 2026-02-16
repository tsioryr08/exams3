bngrc/
│
├── config.php
├── bootstrap.php
├── routes.php          ← ROUTING SEULEMENT
│
├── controllers/        ← LOGIQUE MÉTIER
│   ├── VilleController.php
│   ├── BesoinController.php
│   ├── DonController.php
│   └── DispatchController.php
│
├── models/            ← REQUÊTES SQL
│   ├── Ville.php
│   ├── Besoin.php
│   ├── Don.php
│   └── Dispatch.php
│
└── views/
    └── pages/