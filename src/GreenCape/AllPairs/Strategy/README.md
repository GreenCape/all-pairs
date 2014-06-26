# Strategies

**GreenCape AllPairs** supports different strategies for the generation of test sets.

## Default Strategy

The default strategy is fast, but not very efficient on large parameter sets.

## QICT Strategy

The QICT strategy is a nearly 1:1 PHP port of the algorithm developed by James McCaffrey.

## Comparision

The following tables should help you to find the right strategy for you purpose. They show the number of test sets
generated and the time it took on an average PC.

### Number of Test Sets

<table>
<caption>Number of generated test sets</caption>
<tr><td></td><th>Default</th><th>QICT</th></tr>
<tr><th>testData.txt</th><td>12</td><td>12</td></tr>
<tr><th>server.txt</th><td>25</td><td>25</td></tr>
<tr><th>prime.txt</th><td>35</td><td>35</td></tr>
<tr><th>big.txt</th><td>411</td><td>256</td></tr>
</table>

### Computing time

<table>
<caption>Computing time in seconds</caption>
<tr><td></td><th>Default</th><th>QICT</th></tr>
<tr><th>testData.txt</th><td>0.0027</td><td>0,12</td></tr>
<tr><th>server.txt</th><td>0.0068</td><td>0.04</td></tr>
<tr><th>prime.txt</th><td>0.0072</td><td>0.14</td></tr>
<tr><th>big.txt</th><td>10.0269</td><td>1675.16</td></tr>
</table>

### Test Set Quality

#### testData.txt

<table>
<caption>Generated test sets for testData.txt</caption>
<tr><th>Default</th><th>QICT</th></tr>
<tr><td><pre>Param0	Param1	Param2
a	c	j
b	c	k
b	c	j
a	d	k
a	d	j
b	d	k
b	e	j
a	e	k
a	e	j
b	f	k
b	f	j
a	f	k</pre></td><td><pre>Param0	Param1	Param2
a	c	j
b	d	k
b	e	j
a	f	k
a	c	k
a	d	j
b	f	j
a	e	k
a	d	j
b	c	j
a	e	j
a	f	j</pre></td></tr>
</table>

#### prime.txt

<table>
<caption>Generated test sets for prime.txt</caption>
<tr><th>Default</th><th>QICT</th></tr>
<tr><td><pre>A	B	C	D
A1	B1	C1	D1
A2	B2	C2	D1
A1	B3	C3	D1
A2	B1	C4	D1
A1	B2	C5	D1
A2	B2	C1	D2
A1	B1	C2	D2
A2	B1	C3	D2
A1	B3	C4	D2
A2	B3	C5	D2
A2	B3	C1	D3
A1	B1	C2	D3
A1	B2	C3	D3
A2	B2	C4	D3
A2	B1	C5	D3
A1	B1	C1	D4
A2	B3	C2	D4
A1	B2	C3	D4
A2	B1	C4	D4
A1	B2	C5	D4
A2	B2	C1	D5
A1	B3	C2	D5
A2	B1	C3	D5
A1	B2	C4	D5
A1	B1	C5	D5
A1	B3	C1	D6
A2	B2	C2	D6
A2	B1	C3	D6
A1	B3	C4	D6
A1	B1	C5	D6
A1	B1	C1	D7
A2	B2	C2	D7
A2	B3	C3	D7
A1	B1	C4	D7
A2	B3	C5	D7</pre></td><td><pre>A	B	C	D
A1	B1	C1	D1
A2	B2	C2	D1
A1	B3	C2	D2
A2	B1	C3	D2
A1	B2	C4	D3
A2	B3	C5	D3
A2	B2	C1	D4
A1	B2	C3	D5
A2	B1	C4	D6
A1	B1	C5	D7
A1	B3	C3	D4
A2	B3	C1	D5
A1	B1	C2	D6
A2	B3	C4	D7
A1	B2	C5	D1
A1	B2	C1	D2
A1	B1	C2	D3
A1	B2	C3	D6
A1	B1	C4	D4
A1	B1	C5	D5
A1	B2	C1	D7
A1	B1	C2	D4
A1	B3	C3	D1
A1	B1	C4	D2
A1	B3	C5	D6
A1	B1	C1	D3
A1	B1	C1	D6
A1	B1	C2	D5
A1	B1	C2	D7
A1	B1	C3	D3
A1	B1	C3	D7
A1	B1	C4	D1
A1	B1	C4	D5
A1	B1	C5	D2
A1	B1	C5	D4</pre></td></tr>
</table>

#### server.txt

<table>
<caption>Generated test sets for server.txt (abbreviated for readability)</caption>
<tr><th>Default</th><th>QICT</th></tr>
<tr><td><pre>OS	Websrv.	DBMS	PHP Version
Windows	Apache	MySQL	PHP 5.2
Linux	Nginx	MariaDB	PHP 5.2
Windows	MS-IIS	MS-SQL	PHP 5.2
Linux	LiteSp.	Postgr.	PHP 5.2
Linux	Lightt.	MySQL	PHP 5.2
Windows	Apache	MariaDB	PHP 5.3
Windows	Nginx	MySQL	PHP 5.3
Linux	MS-IIS	Postgr.	PHP 5.3
Linux	LiteSp.	MS-SQL	PHP 5.3
Windows	Lightt.	MariaDB	PHP 5.3
Linux	Apache	MS-SQL	PHP 5.4
Windows	Nginx	Postgr.	PHP 5.4
Linux	MS-IIS	MySQL	PHP 5.4
Windows	LiteSp.	MariaDB	PHP 5.4
Windows	Lightt.	MS-SQL	PHP 5.4
Windows	Apache	Postgr.	PHP 5.5
Linux	Nginx	MS-SQL	PHP 5.5
Linux	MS-IIS	MariaDB	PHP 5.5
Windows	LiteSp.	MySQL	PHP 5.5
Linux	Lightt.	Postgr.	PHP 5.5
Linux	Apache	MySQL	PHP 5.6
Windows	Nginx	MariaDB	PHP 5.6
Windows	MS-IIS	MS-SQL	PHP 5.6
Windows	LiteSp.	Postgr.	PHP 5.6
Linux	Lightt.	MySQL	PHP 5.6</pre></td><td><pre>OS	Websrv.	DBMS	PHP Version
Windows	Apache	MySQL	PHP 5.2
Linux	Nginx	MariaDB	PHP 5.2
Windows	Nginx	MS-SQL	PHP 5.3
Linux	Apache	Postgr.	PHP 5.3
Windows	MS-IIS	MariaDB	PHP 5.4
Windows	LiteSp.	Postgr.	PHP 5.5
Linux	Lightt.	MySQL	PHP 5.6
Linux	MS-IIS	MS-SQL	PHP 5.5
Linux	LiteSp.	MySQL	PHP 5.4
Windows	Lightt.	MariaDB	PHP 5.3
Windows	Apache	MS-SQL	PHP 5.6
Windows	Nginx	Postgr.	PHP 5.4
Windows	MS-IIS	Postgr.	PHP 5.2
Windows	LiteSp.	MariaDB	PHP 5.6
Windows	Lightt.	MySQL	PHP 5.5
Windows	LiteSp.	MS-SQL	PHP 5.2
Windows	Apache	MariaDB	PHP 5.4
Windows	Nginx	MySQL	PHP 5.5
Windows	MS-IIS	MySQL	PHP 5.3
Windows	Lightt.	MS-SQL	PHP 5.4
Windows	Nginx	Postgr.	PHP 5.6
Windows	Lightt.	Postgr.	PHP 5.2
Windows	Apache	MariaDB	PHP 5.5
Windows	MS-IIS	MySQL	PHP 5.6
Windows	LiteSp.	MySQL	PHP 5.3</pre></td></tr>
</table>
