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

To judge the quality of an algorithm, the lower and upper bound for the number of test sets needed to cover all pairs
can be used. A lower bound is given by the product of the size of the two largest parameter sets, because all pairs
made up from the two parameters have to be covered. An upper bound is given by all possible combinations.

<table>
<caption>Number of generated test sets</caption>
<tr><td></td><th>Lower Bound</th><th>Default</th><th>QICT</th><th>Upper Bound</th></tr>
<tr><th>testData.txt</th><td>4*3 = 12</td><td>12</td><td>12</td><td>2*4*3*2 = 48</td></tr>
<tr><th>hardware.txt</th><td>(4*4)*(4*3) = 192</td><td>208</td><td>208</td><td>3*3*4*2*4*4 = 1,152</td></tr>
<tr><th>server.txt</th><td>5*5 = 25</td><td>25</td><td>25</td><td>2*5*4*5 = 200</td></tr>
<tr><th>prime.txt</th><td>7*5 = 35</td><td>35</td><td>35</td><td>2*3*5*7 = 210</td></tr>
<tr><th>volume.txt</th><td>8*7 = 56</td><td>60</td><td>60</td><td>7*7*2*3*8*2 = 4,704</td></tr>
<tr><th>big.txt</th><td>10*10 = 100</td><td>411</td><td>256</td><td>10<sup>26</sup> = 100,000,000,000,000,000,000,000,000</td></tr>
</table>

### Computing time

<table>
<caption>Computing time in seconds</caption>
<tr><td></td><th>Default</th><th>QICT</th></tr>
<tr><th>testData.txt</th><td>0.0027</td><td>0.12</td></tr>
<tr><th>hardware.txt</th><td>0.0150</td><td>0.80</td></tr>
<tr><th>server.txt</th><td>0.0068</td><td>0.04</td></tr>
<tr><th>prime.txt</th><td>0.0072</td><td>0.14</td></tr>
<tr><th>volume.txt</th><td>0.0284</td><td>0.78</td></tr>
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

#### hardware.txt

<table>
<caption>Generated test sets for hardware.txt (abbreviated for readability)</caption>
<tr><th>Default</th><th>QICT</th></tr>
<tr><td><pre>PLATF.	CPUS	RAM	HDD	OS	IE
x86	Single	128MB	SCSI	NT4	4.0
ia64	Dual	1GB	IDE	Win2K	4.0
amd64	Quad	4GB	SCSI	WinXP	4.0
ia64	Single	64GB	IDE	Win2K3	4.0
x86	Quad	1GB	IDE	NT4	5.0
amd64	Single	128MB	SCSI	Win2K	5.0
x86	Dual	64GB	SCSI	WinXP	5.0
ia64	Dual	4GB	SCSI	Win2K3	5.0
ia64	Single	4GB	IDE	NT4	5.5
x86	Quad	64GB	SCSI	Win2K	5.5
amd64	Dual	128MB	IDE	WinXP	5.5
amd64	Single	1GB	SCSI	Win2K3	5.5
amd64	Dual	64GB	IDE	NT4	6.0
x86	Single	4GB	IDE	Win2K	6.0
ia64	Single	1GB	SCSI	WinXP	6.0
ia64	Quad	128MB	IDE	Win2K3	6.0
x86	Dual	128MB	SCSI	Win2K3	4.0</pre></td><td><pre>PLATF.	CPUS	RAM	HDD	OS	IE
x86	Single	128MB	SCSI	NT4	4.0
ia64	Dual	128MB	IDE	Win2K	5.0
amd64	Quad	1GB	IDE	NT4	5.5
ia64	Quad	4GB	SCSI	WinXP	6.0
amd64	Single	64GB	SCSI	Win2K3	5.0
x86	Dual	64GB	IDE	WinXP	4.0
x86	Single	1GB	SCSI	Win2K	5.5
x86	Single	4GB	IDE	Win2K3	6.0
amd64	Dual	4GB	SCSI	NT4	6.0
ia64	Dual	1GB	SCSI	Win2K3	4.0
amd64	Quad	128MB	SCSI	Win2K	4.0
ia64	Single	64GB	SCSI	NT4	5.5
x86	Quad	1GB	SCSI	WinXP	5.0
amd64	Single	128MB	SCSI	WinXP	5.5
x86	Single	4GB	SCSI	Win2K	4.0
x86	Quad	64GB	SCSI	Win2K	6.0
x86	Quad	128MB	SCSI	Win2K3	5.5
x86	Dual	4GB	SCSI	NT4	5.5
x86	Single	128MB	SCSI	NT4	6.0
x86	Single	1GB	SCSI	NT4	6.0
x86	Single	4GB	SCSI	NT4	5.0</pre></td></tr>
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

#### volume.txt

<table>
<caption>Generated test sets for volume.txt</caption>
<tr><th>Default</th><th>QICT</th></tr>
<tr><td><pre>TYPE	SIZE	FORMAT	FSYSTEM	CLUSTER	COMPRESSION
Primary	10	quick	FAT	512	on
Logical	100	slow	FAT32	512	off
Single	500	slow	NTFS	512	on
Span	1000	quick	FAT	512	off
Stripe	5000	quick	FAT32	512	on
Mirror	10000	quick	NTFS	512	off
RAID-5	40000	slow	FAT	512	on
Primary	100	quick	NTFS	1024	on
Logical	10	slow	FAT	1024	off
Single	1000	slow	FAT32	1024	on
Span	500	quick	FAT32	1024	off
Stripe	10000	slow	FAT	1024	on
Mirror	5000	slow	FAT	1024	off
RAID-5	10	quick	NTFS	1024	off
Primary	500	slow	FAT	2048	off
Logical	1000	quick	NTFS	2048	on
Single	10	quick	FAT32	2048	off
Span	100	quick	FAT	2048	on
Stripe	40000	slow	NTFS	2048	off
Mirror	10	slow	FAT32	2048	on
RAID-5	5000	quick	FAT32	2048	on
Primary	1000	slow	FAT32	4096	off
Logical	500	quick	FAT	4096	on
Single	100	quick	FAT	4096	off
Span	10	slow	NTFS	4096	on
Stripe	10	quick	FAT32	4096	off
Mirror	40000	quick	FAT32	4096	on
RAID-5	10000	slow	NTFS	4096	off
Primary	5000	slow	NTFS	8192	off
Logical	10000	slow	FAT32	8192	on
Single	40000	quick	FAT	8192	off
Span	10	slow	NTFS	8192	on
Stripe	100	slow	FAT	8192	on
Mirror	500	quick	FAT32	8192	off
RAID-5	1000	quick	FAT	8192	on
Primary	10000	quick	FAT32	16384	on
Logical	5000	quick	NTFS	16384	off
Single	10	slow	FAT	16384	on
Span	40000	slow	FAT32	16384	off
Stripe	500	quick	NTFS	16384	on
Mirror	100	slow	NTFS	16384	off
RAID-5	100	slow	FAT32	16384	off
Primary	40000	quick	NTFS	32768	on
Logical	10	quick	FAT	32768	off
Single	5000	slow	FAT32	32768	on
Span	10000	slow	FAT	32768	off
Stripe	1000	quick	NTFS	32768	off
Mirror	100	slow	FAT	32768	on
RAID-5	500	slow	FAT32	32768	on
Primary	10	quick	FAT32	65536	off
Logical	40000	slow	NTFS	65536	on
Single	10000	quick	NTFS	65536	off
Span	5000	quick	FAT	65536	on
Stripe	100	slow	FAT32	65536	off
Mirror	1000	slow	FAT	65536	on
RAID-5	500	quick	FAT	65536	off
Primary	1000	slow	FAT	16384	off
Primary	5000	quick	FAT	4096	on
Primary	10000	slow	FAT	2048	off
Primary	40000	quick	FAT32	1024	on</pre></td><td><pre>TYPE	SIZE	FORMAT	FSYSTEM	CLUSTER	COMPRESSION
Primary	10	quick	FAT	512	on
Logical	100	slow	FAT	1024	off
Single	500	quick	FAT32	2048	off
Span	1000	slow	NTFS	2048	on
Stripe	5000	slow	FAT32	4096	on
Mirror	10000	quick	NTFS	4096	off
RAID-5	40000	quick	FAT32	1024	on
Primary	40000	slow	NTFS	8192	off
Logical	100	quick	FAT32	16384	on
Single	10000	slow	FAT	32768	on
Span	5000	quick	FAT	65536	off
Stripe	10	quick	NTFS	32768	off
Mirror	500	slow	FAT	16384	on
RAID-5	1000	slow	FAT	512	off
Primary	10	slow	FAT32	65536	on
Logical	1000	quick	FAT32	8192	on
Single	100	quick	NTFS	512	on
Span	500	quick	FAT32	512	on
Stripe	10000	quick	FAT	2048	on
Mirror	5000	quick	FAT32	32768	on
RAID-5	10	quick	FAT	4096	on
Single	40000	quick	FAT	16384	off
Primary	500	quick	NTFS	1024	on
Logical	500	quick	NTFS	65536	on
Span	10	quick	FAT	8192	on
Stripe	100	quick	FAT	8192	on
Mirror	1000	quick	FAT	1024	on
RAID-5	5000	quick	NTFS	16384	on
RAID-5	10000	quick	FAT32	65536	on
Logical	40000	quick	FAT	2048	on
Primary	100	quick	FAT	4096	on
Span	100	quick	FAT	32768	on
Single	1000	quick	FAT	4096	on
Stripe	40000	quick	FAT	512	on
Mirror	10	quick	FAT	2048	on
Single	5000	quick	FAT	1024	on
Primary	10000	quick	FAT	8192	on
Primary	1000	quick	FAT	16384	on
Primary	5000	quick	FAT	2048	on
Logical	10	quick	FAT	512	on
Logical	5000	quick	FAT	512	on
Logical	10000	quick	FAT	512	on
Single	10	quick	FAT	1024	on
Span	10000	quick	FAT	1024	on
Span	40000	quick	FAT	4096	on
Stripe	500	quick	FAT	1024	on
Stripe	1000	quick	FAT	65536	on
Mirror	100	quick	FAT	65536	on
Mirror	40000	quick	FAT	512	on
RAID-5	100	quick	FAT	2048	on
RAID-5	500	quick	FAT	8192	on
Primary	500	quick	FAT	32768	on
Logical	500	quick	FAT	4096	on
Logical	1000	quick	FAT	32768	on
Single	5000	quick	FAT	8192	on
Single	40000	quick	FAT	65536	on
Span	10	quick	FAT	16384	on
Stripe	10000	quick	FAT	16384	on
Mirror	10	quick	FAT	8192	on
RAID-5	40000	quick	FAT	32768	on</pre></td></tr>
</table>

