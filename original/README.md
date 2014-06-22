# Pairwise Testing with QICT
by James McCaffrey, published in [MSDN Magazine December 2009 Issue](http://msdn.microsoft.com/en-us/magazine/ee819137.aspx)

James McCaffrey explains exactly what pairwise testing is and provides us with complete C# source code for a
production-quality pairwise testing tool named QICT.

## Why yet another pairwise test set generator?

There are several reasons.  First, although PICT is a wonderful tool, it is written in native C++ code and the
source code is not available. The QICT tool written by James McCaffrey is a production-quality pairwise tool written
with managed C# code. The availability of the code allows you to freely modify QICT to meet your own needs.
For example, you can modify QICT to directly read its input from an XML file or a SQL database, or you can modify QICT
to directly emit results in a custom output format. And you may want to experiment with the tool’s logic, say,
for example, by introducing constraints (test input sets that are not permitted), by introducing required test sets,
or changing how the tool generates its test set collection. Additionally, the availability of QICT source code allows
you to copy and place pairwise test set generation code directly into a .NET application or test tool.

Finally, although source code for a few pairwise test set generation tools is available on the Internet,
some of these tools are quite inefficient. For example, consider a situation with 20 parameters,
each of which has 10 values.
For this scenario there are 10 * 10 * 10 * ... * 10 (20 times) = 10^20 = 100,000,000,000,000,000,000 possible
test-case inputs. This is a lot of test cases.
The PICT tool reduces this to only 217 pairwise test sets, and the QICT tool produces either 219 or 216 test sets
(depending upon the seed value of a random number generator). However, one widely referenced pairwise test set
generation tool written in Perl produces 664 sets. Finally, with the QICT source code available and the explanation
of the algorithms used, you can recast QICT to other languages, such as Perl, Python, Java or JavaScript if you wish.

(Text taken from the article mentioned above)
