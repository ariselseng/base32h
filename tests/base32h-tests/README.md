# Base32H Test Suite

## What is it?

A collection of test cases for Base32H, a new(-ish) duotrigesimal
(a.k.a. base-32) number representation.  These test cases exist as a
collection of comma-separated value (CSV) files for implementors to
use in validating the correctness of their numeric and binary
encoders/decoders.

## How do I use it?

If you're implementing a Base32H encoder/decoder library, you should
reference these files as part of your library's test scripts, e.g. by
including a testing step that downloads these test files and runs
through the test cases.

## Am I allowed to use it?

Of course!  These test cases (like the Base32H number system itself)
are dual-licensed under a public domain dedication and (for
jurisdictions which don't recognize the validity of public domain
dedications) the Zero-Clause BSD (0BSD) license.  See `COPYING` in
this repo for details.
