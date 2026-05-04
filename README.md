# 5250 to Web UI

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php)](https://php.net)
[![RPG](https://img.shields.io/badge/RPG-IV_Free-00599C?logo=ibm)](https://www.ibm.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

**Reference implementation: replacing 5250 green-screen interfaces with modern web UIs.**

---

## 🎯 Purpose

Thousands of IBM i (AS400) applications still use 5250 green-screen terminals. This repository demonstrates how to:
- Extract business logic from RPG subfile programs
- Build modern web interfaces that replicate the functionality
- Run both systems in parallel during migration

---

## 📁 Structure

```
5250-to-web-ui/
├── src/
│   ├── RPG/                   # RPG programs (green screen logic)
│   │   ├── CustomerInquiry.rpgle
│   │   └── OrderEntry.rpgle
│   ├── PHP/
│   │   ├── Controller/        # Web controllers
│   │   ├── Service/           # Screen mapping service
│   │   └── View/              # HTML views
├── tests/
│   └── PHP/                   # PHP unit tests
├── docker/
└── README.md
```

---

## 👤 Author

**Joshua** — Neuraldev LLC
GitHub: [@joalvarezweingartt](https://github.com/joalvarezweingartt)
