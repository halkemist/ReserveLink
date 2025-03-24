# 🚀 ReserveLink

ReserveLink is an online appointment scheduling platform.

This is inspired by existing products like [cal](https://cal.com/) or [calendly](https://calendly.com/).

## Features

- Set up your availability days and slots
- Get a link to your public calendar
- Access a user's booking calendar
- Book a meeting
- Receive confirmation email
- Get a unique link to a videoconferencing session
- Get an .ics file to add the meeting schedule to your Google, Apple, Outlook and Thunderbird calendar
- Cancel a meeting

## 🔧 Stack

[![Laravel](https://img.shields.io/badge/Laravel-%23FF2D20.svg?logo=laravel&logoColor=white)](#)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=fff)](#)
[![Docker](https://img.shields.io/badge/Docker-2496ED?logo=docker&logoColor=fff)](#)
[![TailwindCSS](https://img.shields.io/badge/Tailwind%20CSS-%2338B2AC.svg?logo=tailwind-css&logoColor=white)](#)
[![Vite](https://img.shields.io/badge/Vite-646CFF?logo=vite&logoColor=fff)](#)

## Setup

Requirements:
- Docker

#### Move into the app folder
```bash
cd reserve-link/
```

#### Create the .env from the .env.example
```bash
cp .env.example .env
```

#### Install backend dependencies using docker (only first time)
```bash
sudo docker run --rm -v $(pwd):/app -w /app composer install
```

#### Build, create and start the containers (demon, every day usage)
```bash
./vendor/bin/sail up -d
```

#### Install backend dependencies (every day usage)
```bash
./vendor/bin/sail composer install
```

#### Migrate
```bash
./vendor/bin/sail artisan migrate
```

#### Install and compile frontend dependencies
```bash
./vendor/bin/sail npm install && npm run dev
```

### Ports

- 8080 -> PhpMyAdmin

- 8025 -> Mailpit dashboard

## Architecture

- MVC

MCD: https://github.com/halkemist/ReserveLink/blob/main/docs/ReserveLink_diagram.png
CI: https://github.com/halkemist/ReserveLink/blob/main/.github/workflows/ci.yml

## Workflow

#### Create a availability slot and get your calendar link

Auth User -> Dashboard -> Create Availability -> Copy Calendar Link

#### Book a meeting

Guest User -> Access from a calendar link -> Choose a slot -> Put your email -> Receive a confirmation email with the meeting link and ics file
