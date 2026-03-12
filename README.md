# CODENAME: PROJECT ALPHA

> A github-like web application with focus on documentation.

## Motivation

### Aim

Main aim of this project is to create a web-based documentation tool while being developer-friendly similarly to how [Github](https://github.com) handles code.

### Personal motivation

My main motivation for creating this project is to challange myself as well as learn modern web application creation process and create my own documentation tool.

## Folder structure

- **/backend** -> folder used for laravel backend.

- **/frontend** -> folder used for Vite frontend.

## Git Workflow

### The git flow inside this repository follows Github Flow with following branch names

- **'main'** -> main/master, default, long-lived branch. Every commit to this branch should be a working state of app.

- **'dev/*'** -> development, daily workspace for building, always a branch of stable main branch. Merged back to main when feature is done using '--no-ff' flag.

- **'hotfix/*'** -> a hotfix branch, a quick hotfix branch branched off main for swift bugfixing. Merged back into main.

### Additionally, this repository uses Git Tags

- **'release/*'** -> milestone, marks a commit of main branch with a specified 'release-ready' version tag like release/v1.0.0.

### Commit policy

#### Commits should follow imperative Mood. The commit message should complete this sentence

> *"If applied, this commit will..."*

Additionally each commit's subject should be simple and short (up to 72 characters).

#### Prefix system in place

The prefix system is inspired by [Conventional Commits](https://www.conventionalcommits.org).

- "feat:" should signify a new feature -> feat: add user authentication

- "fix:" should signify a bug fix -> fix: resolve crash on logout

- "docs:" should signify a documentation update -> docs: update comments in example.php

- "chore:" should signify a maintenance task -> chore: consolidate gitignore files

- "refactor:" should signify a code cleanup without feature change -> refactor: simplify database connection logic
