# CSV Studio — v2.16 STURDY BUILD 🏆

A fast, lightweight CSV editor for Windows + XAMPP — built for people who manage **dozens of CSV files in nested folders** and are tired of Excel.

Tested in real life with 100s of CSVs + 1000s of images. Sturdy, not perfect — and that's the point. PRs welcome.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4)
![License](https://img.shields.io/badge/License-MIT-green)
![Status](https://img.shields.io/badge/Status-STURDY%20BUILD-gold)

## Why not Excel?

- Excel mangles dates, leading zeros, long numbers
- Excel can't browse nested folders + search inside 100s of CSVs at once
- Excel has no image thumbnails / slideshow for Picture columns
- Excel is slow when you open/close 50 files a day

CSV Studio fixes all of that.

## Features (v2.16)

### 📁 File Explorer
- Recursive folder tree, any drive/folder: `D:/`, `E:/`, `C:/Users/You/Pictures`
- Filter filenames, Up button, Go to path
- Shows file size + count

### 🔍 Smart Search
- Search inside ALL CSVs: `MrJohn`, `John Sr`, `14-10-2024`
- Normalization: ignores case, spaces, special chars — `MrJohn` finds `Mr John`, `MR-JOHN`
- Multiple terms = AND logic
- Highlights matches with `<mark>`

### ✏️ Excel-like Editing
- Double-click to edit
- **Tab** = next cell, **Shift+Tab** = prev cell
- **Enter** = next row, **Shift+Enter** = prev row
- **Esc** = cancel
- Alt+Tab safe — edit stays alive, focus returns
- Copy button for Date column

### 🧱 Manage Columns
- Rename, reorder ↑ ↓, delete, add column
- Replaces old `+ Column` link

### 🖼️ Images
- Columns named `Picture / Photo / Image / Chart / Album / Pic` show 36×36 thumbnails
- Click thumbnail → preview (Esc to close)
- **Slideshow** button when image column exists — ← → arrows, Esc to close, row data below image
- **Browse** button → browse ANY folder (recursive ON/OFF, filter)
- **Windows Picker** → native Windows file dialog — file is copied to current folder with correct `D:/` slash format for XAMPP. No more typing paths!

### 🔎 Filter Current Rows
- Dropdown: `All columns | Date | Pair | Direction...`
- Filter works only on selected column

### 💾 Save
- Save (.csv)
- Save As .csv / .tsv (Copy) — prompts for filename, original stays open
- Auto backup: `backups/filename-timestamp.bak` on every save
- Unsaved changes warning when switching files

### ⌨️ Other
- Column sort ↑ ↓ (smart date/number parse)
- Font size A- / A / A+
- Sidebar resize + collapse

## Quick Start

### Requirements
- Windows + [XAMPP](https://www.apachefriends.org/) (Apache + PHP 7.4+)
- No database, no Node, no build step

### Install (30 seconds)
1. Download latest release ZIP
2. Delete old `csv-studio` folder contents (if upgrading)
3. Extract to `D:/xampp/htdocs/csv-studio/` (or `C:/xampp/htdocs/csv-studio/`)
4. Edit `config.php`:
```php
$DEFAULT_ROOT = 'D:/web/csv/studio/csv_data'; // your CSV folder
```
5. Open in browser: `http://localhost/csv-studio/`
6. Press `Ctrl+F5` to hard refresh

### Folder Structure
```
csv-studio/
├── index.php      # UI
├── api.php        # list / read / write / image / search / upload
├── config.php     # DEFAULT_ROOT
├── csv_data/      # your CSVs (or point config to your folder)
├── backups/       # auto .bak files
└── images/        # uploaded images via Windows Picker
```

## Roadmap / Ideas Welcome
- [ ] Bulk image replace
- [ ] Ctrl+S to save
- [ ] Dark mode
- [ ] Multi-file search & replace
- [ ] CSV validation / duplicate check

Open an Issue if you want one of these!

## Contributing
1. Fork the repo
2. Create a branch: `git checkout -b fix/my-fix`
3. Test with `http://localhost/csv-studio/` + `Ctrl+F5`
4. Commit + Push + Open a Pull Request

Small, focused PRs are best. One bug = one PR.

## Known Limitations
- No software is fool-proof. CSV Studio is sturdy, not perfect.
- Image grid shows up to 300 images per folder (use filter or Windows Picker for big folders)
- Designed for local XAMPP use — not for public hosting without auth

## License
MIT — use it, fork it, sell it, break it, fix it.

## Credits
Built in rapid iteration with real-world testing: dozens of nested folders, hundreds of CSVs, hundreds of images, Alt+Tab + Tab navigation stress-tested daily.
