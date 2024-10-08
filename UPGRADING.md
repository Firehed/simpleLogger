# Upgrading

## 3.x

Changes:

- Formatting logic has been separated into `FormatterInterface`.

- `Base` adds `public FormatterInterface $formatter`, which allows changing the formatter at runtime (though this is not recommended).

- `DefaultFormatter`, whicih retains the previous log formatting logic, is now a bundled implementation.
  If a logger is constructed without specifying a formatter, this one will be used.

- `ChainLogger` no longer extends `Base`.
  In unlikely situations, this could be a BC break.

- `ChainLogger`'s `level` is now configurable only through a new `level` constructor parameter.

BC Breaks:

- Support for PHP before 8.1 (the oldest version with active support at time of writing) has been dropped.

- `dump()` has been removed without replacement.

- `getCurrentSyslogPriority()` has been removed without replacement.

- `setFormat` and `setRenderExceptions` has been removed from `ConfigurableLoggerInterface` and all implementations.
  Only `setLevel` remains.
  These methods now exist on `DefaultFormatter`, but not the broader `FormatterInterface`.

- `Base`'s `writeLog` method has been replaced by `write`, which now receives the already-formatted message and no context.
  This will only matter if you've directly extended `Base` rather than using one of the packaged implementations.

- Minor: appending newlines to log message is done only in the file-based loggers instead of the root-level formatting tools.
  This should have no effect on most installations, but may subtly change results of `syslog` loggers.
