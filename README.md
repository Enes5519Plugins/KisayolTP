# KısayolTP
Aren't you tired of long-written warp commands? Then this plugin is for you

| Feature | KisayolTP | Other Warp Plugins |
| :-----: | :-------: | :-------: |
| Teleport command | /warp warpName | /warpName |

## Basic API
#### Imports
```php
use Enes5519\KisayolTP\KisayolTP;
```

#### Provider
```php
// @return \Enes5519\KisayolTP\provider\DataProvider
$provider = KisayolTP::getAPI()->getProvider();
// Add warp
$provider->addWarp($warpName, $location, $aliases);
// Delete warp
$provider->deleteWarp($warpName);
// Get all warp (\Enes5519\KisayolTP\Warp[]),
$provider->getAllWarps();
```
[Click](https://github.com/Enes5519/KisayolTP/tree/master/src/Enes5519/KisayolTP/provider) for more information

## TODO
- [ ] Multi-Language
- [ ] Edit mode

## POGGIT
[![Poggit-CI](https://poggit.pmmp.io/ci.badge/Enes5519/KisayolTP/KisayolTP)](https://poggit.pmmp.io/ci/Enes5519/KisayolTP/KisayolTP) <br />