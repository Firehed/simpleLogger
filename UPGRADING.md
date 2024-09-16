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
  The logic for these has been moved to the formatters.
  Only `setLevel` remains.
