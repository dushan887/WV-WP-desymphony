# Desymphony Theme

A modern WordPress theme that includes:

- **PSR-4 Autoloading** via `composer.json`  
- **NPM Build** for front-end assets (Bootstrap, Select2, Cropper)  
- **TailwindCSS** & **PostCSS** for utility-first styling and autoprefixing  
- **Webpack** for bundling JS/CSS from `/src` → `/dist`  

## Installation

1. Place the `desymphony` folder in `wp-content/themes/`.
2. If using Composer, run `composer install` in this folder.
3. Run `npm install` to install Node dependencies.
4. To build assets:
   ```bash
   npm run dev    # development
   npm run build  # production
