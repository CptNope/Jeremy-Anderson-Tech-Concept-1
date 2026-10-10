# Fonts

Source TTFs for two tools. These files are not committed; the subset WOFF2 files the pages load are, in `/fonts/`.

- `design/identity/wordmark.py` outlines the wordmark from Archivo.
- `design/tools/subset_fonts.py` rebuilds `/fonts/*.woff2`: it instances each variable font to the axis ranges in use and subsets it to Latin.

Download the variable fonts from the Google Fonts repository and save them here:

| File to save here | Source |
|---|---|
| `Archivo-Variable.ttf` | https://github.com/google/fonts/blob/main/ofl/archivo/Archivo%5Bwdth%2Cwght%5D.ttf |
| `MartianMono-Variable.ttf` (needed by `subset_fonts.py`) | https://github.com/google/fonts/blob/main/ofl/martianmono/MartianMono%5Bwdth%2Cwght%5D.ttf |

Both are licensed under the SIL Open Font License 1.1, with no Reserved Font Name, so subset copies may keep their names. The license texts ship next to the WOFF2 files in `/fonts/`.
