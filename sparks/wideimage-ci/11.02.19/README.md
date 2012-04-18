## Overview

A packaging of [WideImage](http://wideimage.sourceforge.net/) for [GetSparks](http://getsparks.org/). 

We really do not add any extra magic to the library. Please refer to WideImage's website to learn more about this package. 

## Requirements

1. PHP 5.2+
2. CodeIgniter 2.0.0+
3. GD2

## Usage

```
$this->load->spark('wideimage/11.02.19');

$this->wideimage->{your wide image function call here}

Example: $this->wideimage->load('/tmp/foo.jpg')->resize(500, 500, 'outside')->crop('center', 'center', 300, 300)->saveToFile('/tmp/thumb.jpg');
```

## Author(s) 

* By: WideImage [http://wideimage.sourceforge.net/](http://wideimage.sourceforge.net/)

* Company: Cloudmanic Labs, [http://cloudmanic.com](http://cloudmanic.com)

* By: Spicer Matthews [http://spicermatthews.com](http://spicermatthews.com)


