# Upgrade from 5.1 to 5.2

GearmanBundle 5.2 adds support for Symfony 7. Support for Symfony 4.4, 5.x and
6.x is unchanged, and regular usage (configuration, worker annotations, the
`gearman` client service and the console commands) needs no changes.

The points below only affect applications that extend or override bundle
internals.

## Return types

Symfony 7 declares return types on the methods the bundle implements, so the
bundle now declares them too:

| Class | Method |
|---|---|
| `Command\*Command` | `configure(): void`, `execute(...): int` |
| `DependencyInjection\GearmanExtension` | `load(...): void` |
| `DependencyInjection\Configuration` | `getConfigTreeBuilder(): TreeBuilder` |
| `GearmanBundle` | `boot(): void` |
| `Service\GearmanCacheWrapper` | `clear($cacheDir): void`, `warmUp($cacheDir, $buildDir = null): array`, `isOptional(): bool` |

If you extend one of these classes and override one of these methods, add the
same return type to your method (and the optional `$buildDir` argument to
`warmUp()`), otherwise PHP raises a fatal error.

## Annotation reader

Symfony 7 no longer provides the `annotation_reader` service, so the
`gearman.parser` service now uses its own reader,
`gearman.external.annotation_reader` (a plain
`Doctrine\Common\Annotations\AnnotationReader`).

If you replaced or decorated `annotation_reader` to change how the bundle reads
worker annotations, override or decorate `gearman.external.annotation_reader`
instead. If you redefined `gearman.parser`, update its second argument.

## ContainerAwareInterface

`ContainerAwareInterface` was removed in Symfony 7.

* `Module\JobClass` no longer implements it. Its `setContainer()` method is kept.
* On Symfony 4.4 to 6.4, workers that are not services and implement
  `ContainerAwareInterface` still receive the container, as before.
* On Symfony 7, the container is not injected into workers that are not
  services. Define the worker as a service (the `service` option of the `@Work`
  annotation) and inject its dependencies instead.
