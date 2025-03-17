# Portal UNESP Adapter

A PHP adapter for interacting with the UNESP (Universidade Estadual Paulista "Júlio de Mesquita Filho") portal system.

## Overview

This project provides an adapter to simplify interactions with the UNESP portal, allowing for easier integration with the university's systems.

## Features

- Simplified API for UNESP portal interactions
- Easy integration with existing PHP applications

## Installation

```bash
composer require damiansb/portal-unesp-adapter
```

## Usage

```php
<?php

require 'vendor/autoload.php';

use DamianSB\PortalUnespAdapter\Adapter;

// Initialize the adapter
$adapter = new Adapter([
    // Configuration options
]);
```

## Requirements

- PHP 7.4 or higher
- Composer

## Configuration

Details about configuration options and environment variables needed.

## Documentation

For more detailed documentation, please see the [docs](./docs) directory.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

- José Eduardo Biasioli