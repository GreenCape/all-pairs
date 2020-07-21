# A Pair-wise Testset Generator

Pairwise (a.k.a. all-pairs) testing is an effective test case generation technique that is based on the observation
that most faults are caused by interactions of at most two factors. Pairwise-generated test suites cover all
combinations of two therefore are much smaller than exhaustive ones yet still very effective in finding defects.

## Requirements

@todo

## Versioning

This project follows [semantic versioning](http://semver.org).

## Roadmap

  - <s>Sub-models</s>
  - Conditional constraints
  - Unconditional constraints
  - Command line tool
  - Separator definition (`/d:`)
  - Order (pairs, triples, n-tuples, `/o:`)
  - Aliasing
  - Negative testing
  - Weighting
  - Seeding
  - Randomization
  - Output suitable for PHPUnit (@dataprovider)

## Further Reading

  - [DevelopSense: Pairwise Testing](http://www.developsense.com/pairwiseTesting.html)
  - [A.G.McDowell: All-Pairs Testing](http://www.mcdowella.demon.co.uk/allPairs.html)
  - [NIST: Practical Combinatorial Testing](http://csrc.nist.gov/groups/SNS/acts/documents/SP800-142-101006.pdf) (PDF)
