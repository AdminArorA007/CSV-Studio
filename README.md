# CSV Studio — Sturdy Ver 1.0 🏆

> Excel has zillions of great features, but for certain needs people need just a fraction of those — plus custom features that simply don't exist in Excel. And therefore, CSV Studio.

A fast, lightweight CSV editor for Windows + XAMPP — built for people who manage **dozens of CSV files in nested folders** and are tired of Excel.

Tested in real life with 100s of CSVs + 1000s of images. Sturdy, not perfect — and that's the point. PRs welcome.


## Why not Excel?

- Excel mangles dates, leading zeros, long numbers
- Excel can't browse nested folders + search inside 100s of CSVs at once
- Excel has no image thumbnails / slideshow for Picture columns
- Excel is slow when you open/close 50 files a day
- Excel has no per-save timestamped backups, no Windows Picker image insert, no Smart Search that matches `MrJohn` to `Mr.John`

CSV Studio fixes all of that.

## Features (Sturdy Ver 1.0)

> ⚠️ Sturdy Ver 1.1.2 (Ctrl+S, save toast, drag-select fix, true fullscreen, bottom strip) is in real-life testing locally and is **NOT** on GitHub yet. This README describes only what is actually in this repo: Sturdy Ver 1.0.


- **Browse any drive/folder** — recursive nested folders, collapsed by default, auto-expands to the open file
- **Excel-like editing** — double-click to edit, Tab = 1 cell, Shift+Tab back, Enter = next row, Shift+Enter = previous row
- **Smart Search inside files** — searches all CSVs, normalises punctuation/case, AND logic (`John Sr`)
- **Image / Picture columns** — 36×36 thumbnails, click for preview, Windows Picker to insert (native dialog, correct `D:/` slashes)
- **Manage Columns** — rename, reorder with ↑↓, delete, add
- **Sort, Filter, Select-all** — smart date/number/text sorting, filter respects selection
- **Add / Edit Row forms** — Date auto-filled as DD-MM-YYYY
- **Save / Save As (Copy)** — Save As saves a copy and stays on the original
- **Backups** — before every save: `backups/filename-unix-timestamp.bak`
- **Font size A- / A / A+**, persisted
- **Unsaved-changes warning** when switching files

## Version history

| Version | Notes |
| :--- | :--- |
| **Sturdy Ver 1.0** | True fullscreen: image grows, slideshow row data moves to bottom strip |
| **Sturdy Ver 1.1.1** | Sidebar-blank fix (JS syntax error), sidebar now shows errors instead of going blank, Windows Picker upload restored |
| **Sturdy Ver 1.1** | Ctrl+S, drag-select modal fix, Maximize buttons, custom Browse Images removed, save toast |
| **Sturdy Ver 1.0** | Renamed from v2.16 STURDY BUILD — clean 3-file base, no `*_1.php` patch soup |

> Old name: v2.16 STURDY BUILD = Sturdy Ver 1.0. The `v0.9` label briefly used in the README was a typo — please use Sturdy Ver numbering above.
> The `v2.16` GitHub Release is the same code as Sturdy Ver 1.0.

## Install (Windows + XAMPP)

1. Copy the files to `D:/web/csv/studio/` (or any folder under `htdocs`)
2. Only 3 PHP files: `index.php`, `api.php`, `config.php` (+ `README.md`, `LICENSE`)
3. Edit `config.php` if your CSVs live elsewhere:
   ```php
   $DEFAULT_ROOT = 'D:/web/csv/studio/csv_data';
   ```
4. Open `http://localhost/csv/studio/` (adjust to your folder name)
5. Point the sidebar at any drive/folder with the path box + Go

No database. No build step. No installer.

## Layout

```
csv/studio/
  index.php      # UI
  api.php        # list / read / write / search / images / upload
  config.php     # $DEFAULT_ROOT + backup dir
  csv_data/      # your CSVs (put yours here, or point config elsewhere)
  images/        # images referenced by Picture columns
  backups/       # auto-created timestamped .bak files on every save
```

## Notes

- Only `.csv` files are shown in the sidebar. TSV/TXT are hidden, but misnamed TSVs are auto-detected by delimiter.
- `.gitignore` (created via GitHub web UI) keeps private CSVs/images out of the repo.
- MIT License — see `LICENSE`.

## What's NOT in here (yet)

Bulk find-replace, bulk image replace, freeze columns, chart preview — deliberately left out until Sturdy Ver 1.0 has been tested with real work.
