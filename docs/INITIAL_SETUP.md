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

### Up containers & Compile front end
```bash
cd reserve-link
.vendor/bin/sail up -d
.vendor/bin/sail npm run dev
```

### Run feature tests
```bash
cd reserve-link
./vendor/bin/sail artisan test --testsuite=Feature
```