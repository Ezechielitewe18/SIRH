# Marketing GLOBIT SAAS — dossier visuel

Tout est **prêt à publier**, mais rien n'est publié automatiquement : c'est toi qui cliques.

## Contenu du dossier

| Fichier | Format | Où l'utiliser |
|---|---|---|
| `visuels/flyer-a4.pdf` | PDF A4 (imprimable) | Impression, pièce jointe,*e-mail |
| `visuels/flyer-a4.png` | PNG 794×1123 | WhatsApp, Telegram, LinkedIn |
| `visuels/post-instagram.png` | PNG 1080×1350 | Instagram (portrait) |
| `visuels/post-linkedin.png` | PNG 1200×628 | LinkedIn, X, Facebook (paysage) |
| `visuels/story-whatsapp.png` | PNG 1080×1920 | WhatsApp Status, Instagram Story, TikTok (image de couverture) |
| `visuels/banner-presentation.png` | PNG 1600×900 | Diaporamas, salons, en-tête d'e-mail |
| `captures/*.png` | 11 captures du logiciel | Carrousels, TikToks, démonstration commerciale |
| `textes/captions-et-approche.md` | Textes | 15 publications + messages de prospection + réponses aux objections |
| `sources/*.html` | Sources | Modifiables : c'est ici qu'on change les textes et le téléphone |
| `sources/identite.css` | Charte | Couleurs, polices, composants communs |

## Les captures sont-elles reales ?

Oui. Elles sont prises automatiquement dans le logiciel avec la base **fictive**
`sirh_demo` (9 employés inventés : MUKENDI, KANKU, ILUNGA…) — ta vraie base `sirh`
n'est jamais utilisée pour les visuels.

Régénérer les captures : voir `README.md` à la racine du dossier.

## Personnaliser les visuels

1. Ouvrir `sources/flyer-a4.html` (ou un autre) dans un éditeur de texte
2. Remplacer `+243 00 000 0000` par ton vrai numéro, `contact@globit.com` si besoin
3. Enregistrer, puis régénérer (commande ci-dessous)

## Régénérer les visuels après modification

```powershell
cd C:\xampp\htdocs\SIRH\marketing
$edge = "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
$base = "file:///C:/xampp/htdocs/SIRH/marketing/sources/"
@(@('flyer-a4','794,1123'),@('post-instagram','1080,1350'),@('post-linkedin','1200,628'),
  @('story-whatsapp','1080,1920'),@('banner-presentation','1600,900')) | ForEach-Object {
  & $edge --headless=new --disable-gpu --hide-scrollbars --virtual-time-budget=6000 `
      --window-size=$($_[1]) --screenshot="visuels\$($_[0]).png" ($base + $_[0] + '.html')
}
& $edge --headless=new --disable-gpu --no-pdf-header-footer --virtual-time-budget=9000 `
    --print-to-pdf="visuels\flyer-a4.pdf" ($base + 'flyer-a4.html')
```