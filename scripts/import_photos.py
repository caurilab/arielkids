#!/usr/bin/env python3
"""Copie et renomme les photos sources vers storage/app/public/products/.

Les originaux dans Photos/ ne sont jamais modifiés.
"""
import json
from pathlib import Path
from PIL import Image

ROOT = Path(__file__).resolve().parent.parent
SRC = ROOT / "Photos"
DEST = ROOT / "storage/app/public/products"
DEST.mkdir(parents=True, exist_ok=True)

sources = sorted(
    p for p in SRC.iterdir()
    if p.suffix.lower() in (".png", ".jpg", ".jpeg", ".webp")
)

manifest = json.loads((ROOT / "database/seeders/data/products.json").read_text())

copied = 0
for item in manifest:
    src = sources[item["src"]]
    img = Image.open(src).convert("RGB")
    img.thumbnail((1400, 1400), Image.LANCZOS)
    out = DEST / f"{item['slug']}.jpg"
    img.save(out, "JPEG", quality=82, optimize=True)
    copied += 1

# Logo de la marque
logo_src = sources[57]
logo = Image.open(logo_src).convert("RGB")
logo.thumbnail((600, 600), Image.LANCZOS)
(ROOT / "public/images").mkdir(parents=True, exist_ok=True)
logo.save(ROOT / "public/images/logo.jpg", "JPEG", quality=88)

print(f"{copied} photos copiées vers {DEST}")
print("logo -> public/images/logo.jpg")
