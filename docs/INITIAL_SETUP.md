# Initial Installation & Setup

## Requirements

- Docker

## Installation

```bash
cd reserve-link

# Start docker containers using Sail
./vendor/bin/sail up

# Install JavaScript dependencies
./vendor/bin/sail npm install

# Compile assets
./vendor/bin/sail npm run dev

# Launch migrations
./vendor/bin/sail artisan migrate
```

## Every day usage

```bash
cd reserve-link
.vendor/bin/sail up -d
.vendor/bin/sail npm run dev
```