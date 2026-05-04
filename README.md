# 5250 to Web UI

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-blue)](LICENSE)

A comprehensive toolkit for analyzing, parsing, and converting IBM i 5250 green-screen applications into modern web-based user interfaces.

## Overview

IBM i (AS/400) systems still run thousands of mission-critical applications accessed via 5250 terminal emulation (green screens). This toolkit helps you modernize those applications step by step:

1. **Analyze** existing 5250 display files (DDS) and RPG programs
2. **Parse** screen layouts, fields, validation rules, and navigation flows
3. **Generate** modern web equivalents using HTML, CSS, JavaScript, and your choice of backend

## Features

- **DDS Parser** — Parses IBM i Display Data Specification (DDS) source files into structured metadata
- **Screen Flow Analyzer** — Maps out navigation between screens, subfiles, and option menus
- **UI Generator** — Generates responsive web UIs from parsed screen definitions
- **RPG Integration** — Works alongside existing RPG programs or generates PHP equivalents
- **Subfile Support** — Full support for subfiles (paging, row selection, search)
- **Field Validation** — Preserves DDS validation rules (mandatory, range, compare, etc.)
- **Command Key Mapping** — Maps F-keys and command keys to web buttons/actions

## Installation

```bash
git clone https://github.com/joalvarezweingartt/5250-to-web-ui.git
cd 5250-to-web-ui
composer install
```

## Usage

### Analyze a 5250 Display File (DDS)

```bash
php bin/analyze-5250.php --file path/to/display.dds
```

### Generate a Web UI from DDS Analysis

```bash
php bin/generate-ui.php --input analysis.json --output ./web-ui --template bootstrap5
```

### Start the Development Server

```bash
php -S localhost:8080 -t public
```

## Project Structure

```
5250-to-web-ui/
├── bin/                    # CLI tools
│   ├── analyze-5250.php    # DDS analysis tool
│   └── generate-ui.php     # Web UI generator
├── src/
│   ├── DDS/                # DDS parsing engine
│   │   ├── Parser.php
│   │   ├── Record.php
│   │   ├── Field.php
│   │   └── Keyword.php
│   ├── Analyzer/           # Screen flow analysis
│   │   ├── ScreenFlow.php
│   │   └── DependencyGraph.php
│   ├── Generator/          # Web UI generation
│   │   ├── HtmlGenerator.php
│   │   ├── FormGenerator.php
│   │   ├── SubfileGenerator.php
│   │   └── MenuGenerator.php
│   ├── Transformer/         # Data transformation
│   │   ├── FieldTransformer.php
│   │   └── ValidationTransformer.php
│   └── Utils/
│       └── FileLoader.php
├── templates/              # UI templates
│   ├── bootstrap5/
│   └── minimal/
├── examples/               # Example DDS files & outputs
├── tests/                  # PHPUnit tests
├── docs/                   # Documentation
└── public/                 # Web entry point
```

## Example: Parsing a DDS File

Input (`examples/order-entry.dds`):
```
A          R CUSTOMER                   WINDOW(10 20)
A* Customer Inquiry Screen
A            CUSNO         5A  B  7  2CHECK(01)
A            CUSNO_H      10A  O  7  2
A            NAME         30A  O  8  2
A            ADDR1        30A  O  9  2
A            CITY         20A  O 10  2
A            STATE         2A  O 10 23
A            ZIP          10A  O 10 26
A          R CUSTOMERL                  SFL
A            CUSNO         5A  B  2  2
A            NAME         30A  O  2  8
A            BALANCE       7S 2O  2 40EDTCDR(2)
A          R CUSTCTL                    SFLCTL(CUSTOMERL)
A            SFLPAG(0012)
A            SFLSIZ(0012)
A  23                                   CF03(12 'Cancel')
A  12                                   CF12(12 'Search')
```

Output (generated web form + subfile table).
[View example output →](examples/output/)

## Supported DDS Keywords

| Category | Keywords |
|----------|----------|
| Position | `ROWCOL`, `SPACEA`, `SPACEB` |
| Display | `DSPATR`, `COLOR`, `HIGHLIGHT`, `BLINK` |
| Validation | `CHECK`, `COMP`, `RANGE`, `VALUES`, `ERRMSG` |
| Editing | `EDTCDE`, `EDTWRD`, `EDTMSK` |
| Subfiles | `SFL`, `SFLCTL`, `SFLPAG`, `SFLSIZ`, `SFLRCDNBR`, `SFLDSP`, `SFLDSPCTL` |
| Windows | `WINDOW`, `WDWBORDER` |
| Help | `HELP`, `MSGID` |
| Command Keys | `CFxx`, `CAxx`, `CAnn` |

## License

MIT
