#!/usr/bin/env python3
"""Génère des visuels produits démo pastel pour Ariel Kid's."""
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

OUT = Path("storage/app/public/products/demo")
OUT.mkdir(parents=True, exist_ok=True)

W = H = 800

ITEMS = {
    # clé: (emoji, fond, accent)
    "robe-fleurs": ("👗", "#FDE6EE", "#EE4E80"),
    "ensemble-jupe": ("🎀", "#FFF1E3", "#F5A623"),
    "ballerines": ("🩰", "#FDE6EE", "#D92F66"),
    "sac-licorne": ("🦄", "#F1ECFB", "#8B5CF6"),
    "barrettes": ("🌸", "#FDE6EE", "#F4749E"),
    "tshirt-dino": ("🦕", "#E4F7F0", "#41C9A7"),
    "pantalon-cargo": ("👖", "#E8F4FC", "#4667A6"),
    "baskets-scratch": ("👟", "#E8F4FC", "#35518B"),
    "sac-fusee": ("🚀", "#F1ECFB", "#6886BE"),
    "casquette": ("🧢", "#FFF1E3", "#F5A623"),
    "body-coton": ("🍼", "#E4F7F0", "#41C9A7"),
    "grenouillere": ("🐰", "#FDE6EE", "#F4749E"),
    "chaussons": ("🧸", "#FFF1E3", "#D92F66"),
    "bonnet": ("🧶", "#E8F4FC", "#4667A6"),
    "robe-pagne": ("👗", "#FFF1E3", "#B61F50"),
    "sandales": ("👡", "#FDE6EE", "#EE4E80"),
    "cabas": ("👜", "#E4F7F0", "#41C9A7"),
    "montre-femme": ("⌚", "#F1ECFB", "#8B5CF6"),
    "chemise-lin": ("👔", "#E8F4FC", "#35518B"),
    "mocassins": ("🥿", "#FFF1E3", "#F5A623"),
    "sacoche": ("💼", "#E8F4FC", "#4667A6"),
    "montre-homme": ("⌚", "#E4F7F0", "#232F55"),
}

def font(size):
    for name in ("AppleColorEmoji", "/System/Library/Fonts/Apple Color Emoji.ttc"):
        try:
            return ImageFont.truetype(name, size)
        except Exception:
            continue
    return ImageFont.load_default()

for key, (emoji, bg, accent) in ITEMS.items():
    img = Image.new("RGB", (W, H), bg)
    d = ImageDraw.Draw(img)

    # Cercle pastel central
    cx, cy, r = W // 2, H // 2, 240
    d.ellipse([cx - r, cy - r, cx + r, cy + r], fill="#FFFFFF")

    # Anneau accent
    d.ellipse([cx - r - 18, cy - r - 18, cx + r + 18, cy + r + 18], outline=accent, width=10)

    # Petits pois décoratifs
    for (px, py, pr) in [(90, 120, 26), (690, 90, 18), (110, 660, 20), (680, 680, 28), (620, 180, 12), (170, 240, 12)]:
        d.ellipse([px - pr, py - pr, px + pr, py + pr], fill=accent)

    # Emoji centré
    f = font(300)
    bbox = d.textbbox((0, 0), emoji, font=f)
    tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
    d.text((cx - tw / 2 - bbox[0], cy - th / 2 - bbox[1]), emoji, font=f, fill=accent, embedded_color=True)

    img.save(OUT / f"{key}.png")

print(f"{len(ITEMS)} images générées dans {OUT}")
