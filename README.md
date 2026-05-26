# Asmeninių finansų apskaita

Laravel pagrindu sukurta asmeninių finansų valdymo sistema.

## Funkcionalumas

- Vartotojų registracija ir prisijungimas
- Pajamų ir išlaidų įrašymas su kategorijomis
- Kategorijų valdymas (CRUD)
- Biudžeto planavimas pagal mėnesį
- Kalendoriaus vaizdas
- Ataskaitos pagal 3 pjūvius (periodas, kategorijos, visi įrašai)
- PDF generavimas ir siuntimas el. paštu
- Dashboard su grafikais (Chart.js)

## Technologijos

- PHP 8.2
- Laravel 12
- MySQL
- Tailwind CSS
- Chart.js

## Instaliavimas

```bash
git clone https://github.com/tavo-vardas/finansai.git
cd finansai
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## UML Diagramos

- [DB diagrama](docs/db_diagram.png)
- [Klasių diagrama](docs/class_diagram.png)

## Autorius

Nedas Michailovas – IST-24