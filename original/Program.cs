using System;
using System.Collections.Generic;

using System.IO;

namespace Gamma
{
  class Program
  {
    static void Main(string[] args)
    {
      //string file = "..\\..\\test.txt";
      string file = "..\\..\\testData.txt";
      //string file = "..\\..\\test-mathy.txt";
      //string file = "..\\..\\s1.txt";
      //string file = "..\\..\\s2.txt";
      //string file = "..\\..\\s3.txt";
      //string file = "..\\..\\s4.txt";
      //string file = "..\\..\\s5.txt";
      //string file = "..\\..\\s6.txt";
      //string file = args[0];
      //string file = "..\\..\\ThreeParams.txt";

      Random r = new Random(0);

      int numberParameters = 0;
      int numberParameterValues = 0;
      int numberPairs = 0;
      int poolSize = 20; // number of candidate testSet arrays to generate before picking one to add to testSets List

      int[][] legalValues = null; // in-memory representation of input file as ints
      string[] parameterValues = null; // one-dimensional array of all parameter values
      int[,] allPairsDisplay = null; // rectangular array; does not change, used to generate unusedCounts array
      List<int[]> unusedPairs = null; // changes
      int[,] unusedPairsSearch = null; // square array -- changes
      int[] parameterPositions = null; // the parameter position for a given value
      int[] unusedCounts = null; // count of each parameter value in unusedPairs List
      List<int[]> testSets = null; // the main result data structure

      try
      {
        Console.WriteLine("\nBegin pair-wise testset generation\n");
        //Console.WriteLine("\nInput file = " + file + "\n");

        // do a preliminary file read to determine number of parameters and number of parameter values
        FileStream fs = new FileStream(file, FileMode.Open);
        StreamReader sr = new StreamReader(fs);
        string line;
        while ((line = sr.ReadLine()) != null)
        {
          ++numberParameters;
          string[] lineTokens = line.Split(':');
          string[] strValues = lineTokens[1].Split(',');
          numberParameterValues += strValues.Length;
        }

        Console.WriteLine("There are " + numberParameters + " parameters");
        Console.WriteLine("There are " + numberParameterValues + " parameter values");

        // now do a second file read to create the legalValues array, and the parameterValues array
        fs.Position = 0;

        legalValues = new int[numberParameters][];
        parameterValues = new string[numberParameterValues];
        int currRow = 0;
        int kk = 0; // points into parameterValues
        while ((line = sr.ReadLine()) != null)
        {
          string[] lineTokens = line.Split(':'); // separate parameter name from parameter values (as strings at this point)
          string[] strValues = lineTokens[1].Split(','); // pull out the individual parameter values into an array
          int[] values = new int[strValues.Length]; // create small row array for legalValues

          for (int i = 0; i < strValues.Length; ++i) // trim whitespace
          {
            strValues[i] = strValues[i].Trim();
            values[i] = kk;
            parameterValues[kk] = strValues[i];
            ++kk;
          }

          legalValues[currRow++] = values;
          //for (int i = 0; i < values.Length; ++i)
          //{
          //  values[i] = kk; // map
          //  parameterValues[kk] = 
          //}


        } // while


        sr.Close();
        fs.Close();

        Console.WriteLine("\nParameter values: ");
        for (int i = 0; i < parameterValues.Length; ++i)
          Console.Write(parameterValues[i] + " ");
        Console.WriteLine("");

        Console.WriteLine("\nLegal values internal representation: ");
        for (int i = 0; i < legalValues.Length; ++i)
        {
          Console.Write("Parameter" + i + ": ");
          for (int j = 0; j < legalValues[i].Length; ++j)
          {
            Console.Write(legalValues[i][j] + " ");
          }
          Console.WriteLine("");
        }

        // determine the number of pairs for this input set
        for (int i = 0; i <= legalValues.Length - 2; ++i)
        {
          for (int j = i + 1; j <= legalValues.Length - 1; ++j)
          {
            numberPairs += (legalValues[i].Length * legalValues[j].Length);
          }
        }
        Console.WriteLine("\nThere are " + numberPairs + " pairs ");

        // process the legalValues array to populate the allPairsDisplay & unusedPairs & unusedPairsSearch collections
        allPairsDisplay = new int[numberPairs, 2]; // rectangular array; does not change
        unusedPairs = new List<int[]>(); // List of pairs which have not yet been captured
        unusedPairsSearch = new int[numberParameterValues, numberParameterValues]; // square array -- changes

        int currPair = 0;
        for (int i = 0; i <= legalValues.Length - 2; ++i)
        {
          for (int j = i + 1; j <= legalValues.Length - 1; ++j)
          {
            int[] firstRow = legalValues[i];
            int[] secondRow = legalValues[j];
            for (int x = 0; x < firstRow.Length; ++x)
            {
              for (int y = 0; y < secondRow.Length; ++y)
              {
                allPairsDisplay[currPair, 0] = firstRow[x]; // pair first value
                allPairsDisplay[currPair, 1] = secondRow[y]; // pair second value

                int[] aPair = new int[2];
                aPair[0] = firstRow[x];
                aPair[1] = secondRow[y];
                unusedPairs.Add(aPair);

                unusedPairsSearch[firstRow[x], secondRow[y]] = 1;

                ++currPair;
              } // y
            } // x
          } // j
        } // i

        //Console.WriteLine("allPairsDisplay array:");
        //for (int i = 0; i < numberPairs; ++i)
        //{
        //  Console.WriteLine(i + " " + allPairsDisplay[i, 0] + " " + allPairsDisplay[i, 1]);
        //}

        //Console.WriteLine("unusedPairs array:");
        //for (int i = 0; i < numberPairs; ++i)
        //{
        //  if (unusedPairs[i] != null)
        //  {
        //    Console.WriteLine(i + " " + unusedPairs[i][0] + " " + unusedPairs[i][1]);
        //  }
        //}

        //Console.WriteLine("unusedPairs List<>:");
        //for (int i = 0; i < unusedPairs.Count; ++i)
        //{
        //  int[] curr = unusedPairs[i];
        //  Console.WriteLine(i + " " + curr[0] + " " + curr[1]);
        //}

        //Console.WriteLine("allPairsSearch array:");
        //for (int row = 0; row < numberParameterValues; ++row)
        //{
        //  for (int col = 0; col < numberParameterValues; ++col)
        //  {
        //    Console.Write(allPairsSearch[row, col] + " ");
        //  }
        //  Console.WriteLine("");
        //}

        // process legalValues to populate parameterPositions array
        parameterPositions = new int[numberParameterValues]; // the indexes are parameter values, the cell values are positions within a testSet
        int k = 0;  // points into parameterPositions
        for (int i = 0; i < legalValues.Length; ++i)
        {
          int[] curr = legalValues[i];
          for (int j = 0; j < curr.Length; ++j)
          {
            parameterPositions[k++] = i;
          }
        }
        //Console.WriteLine("parameterPositions:");
        //for (int i = 0; i < parameterPositions.Length; ++i)
        //{
        //  Console.Write(parameterPositions[i] + " ");
        //}
        //Console.WriteLine("");


        // process allPairsDisplay to determine unusedCounts array
        unusedCounts = new int[numberParameterValues];  // inexes are parameter values, cell values are counts of how many times the parameter value apperas in the unusedPairs collection
        for (int i = 0; i < allPairsDisplay.GetLength(0); ++i)
        {
          ++unusedCounts[allPairsDisplay[i, 0]];
          ++unusedCounts[allPairsDisplay[i, 1]];
        }

        //Console.WriteLine("unusedCounts: ");
        //for (int i = 0; i < unusedCounts.Length; ++i)
        //{
        //  Console.Write(unusedCounts[i] + " ");
        //}
        //Console.WriteLine("");

        //==============================================================================================================
        testSets = new List<int[]>();  // primary data structure
        Console.WriteLine("\nComputing testsets which capture all possible pairs . . .");
        while (unusedPairs.Count > 0) // as long as ther are unused pairs to account for . . .
        {
          int[][] candidateSets = new int[poolSize][]; // holds candidate testSets

          for (int candidate = 0; candidate < poolSize; ++candidate)
          {
            int[] testSet = new int[numberParameters]; // make an empty candidate testSet

            // pick "best" unusedPair -- the pair which has the sum of the most unused values
            int bestWeight = 0;
            int indexOfBestPair = 0;
            for (int i = 0; i < unusedPairs.Count; ++i)
            {
              int[] curr = unusedPairs[i];
              int weight = unusedCounts[curr[0]] + unusedCounts[curr[1]];
              if (weight > bestWeight)
              {
                bestWeight = weight;
                indexOfBestPair = i;
              }
            }

            //// pick best unusedPair, starting at a random index -- does not seem to help any
            //int bestWeight = 0;
            //int indexOfBestPair = 0;
            //int currIndex = r.Next(unusedPairs.Count);
            //for (int ct = 0; ct < unusedPairs.Count; ++ct) // count is predetermine
            //{
            //  if (currIndex == unusedPairs.Count - 1) // if at end of unusedPairs, jump to beginnng
            //  {
            //    currIndex = 0;
            //  }
            //  int[] curr = unusedPairs[currIndex];
            //  int weight = unusedCounts[curr[0]] + unusedCounts[curr[1]];
            //  if (weight > bestWeight)
            //  {
            //    bestWeight = weight;
            //    indexOfBestPair = currIndex;
            //  }
            //  ++currIndex;
            //}

            int[] best = new int[2]; // a copy is not strictly necesary here
            unusedPairs[indexOfBestPair].CopyTo(best, 0);

            //Console.WriteLine("Best pair is " + best[0] + ", " + best[1] + " at " + indexOfBestPair + " with weight " + bestWeight);

            int firstPos = parameterPositions[best[0]]; // position of first value from best unused pair
            int secondPos = parameterPositions[best[1]];

            //Console.WriteLine("The best pair belongs at positions " + firstPos + " and " + secondPos);

            // generate a random order to fill parameter positions
            int[] ordering = new int[numberParameters];
            for (int i = 0; i < numberParameters; ++i) // initially all in order
              ordering[i] = i;

            // put firstPos at ordering[0] && secondPos at ordering[1]
            ordering[0] = firstPos;
            ordering[firstPos] = 0;

            int t = ordering[1];
            ordering[1] = secondPos;
            ordering[secondPos] = t;

            // shuffle ordering[2] thru ordering[last]
            for (int i = 2; i < ordering.Length; i++)  // Knuth shuffle. start at i=2 because want first two slots left alone
            {
              int j = r.Next(i, ordering.Length);
              int temp = ordering[j];
              ordering[j] = ordering[i];
              ordering[i] = temp;
            }

            //Console.WriteLine("Order: ");
            //for (int i = 0; i < ordering.Length; ++i)
            //  Console.Write(ordering[i] + " ");
            //Console.WriteLine("");
            //Console.ReadLine();

            // place two parameter values from best unused pair into candidate testSet
            testSet[firstPos] = best[0];
            testSet[secondPos] = best[1];
            //Console.WriteLine("Placed params " + best[0] + " " + best[1] + " at " + firstPos + " and " + secondPos);
            //Console.ReadLine();

            // for remaining parameter positions in candidate testSet, try each possible legal value, picking the one which captures the most unused pairs . . .
            for (int i = 2; i < numberParameters; ++i) // start at 2 because first two parameter have been placed
            {
              int currPos = ordering[i];
              int[] possibleValues = legalValues[currPos];
              //Console.WriteLine("possibles are ");
              //for (int z = 0; z < possibleValues.Length; ++z)
              //  Console.WriteLine(possibleValues[z]);
              //Console.WriteLine("");

              int currentCount = 0;  // count the unusedPairs grabbed by adding a possible value
              int highestCount = 0;  // highest of these counts
              int bestJ = 0;         // index of the possible value which yields the highestCount
              for (int j = 0; j < possibleValues.Length; ++j) // examine pairs created by each possible value and each parameter value already there
              {
                currentCount = 0;
                for (int p = 0; p < i; ++p)  // parameters already placed
                {
                  int[] candidatePair = new int[] { possibleValues[j], testSet[ordering[p]] };
                  //Console.WriteLine("Considering pair " + possibleValues[j] + ", " + testSet[ordering[p]]);

                  if (unusedPairsSearch[candidatePair[0], candidatePair[1]] == 1 ||
                    unusedPairsSearch[candidatePair[1], candidatePair[0]] == 1)  // because of the random order of positions, must check both possibilities
                  {
                    //Console.WriteLine("Found " + candidatePair[0] + "," + candidatePair[1] + " in unusedPairs");
                    ++currentCount;
                  }
                  else
                  {
                    //Console.WriteLine("Did NOT find " + candidatePair[0] + "," + candidatePair[1] + " in unusedPairs");
                  }
                } // p -- each previously placed paramter
                if (currentCount > highestCount)
                {
                  highestCount = currentCount;
                  bestJ = j;
                }

              } // j -- each possible value at currPos
              //Console.WriteLine("The best value is " + possibleValues[bestJ] + " with count = " + highestCount);

              testSet[currPos] = possibleValues[bestJ]; // place the value which captured the most pairs
            } // i -- each testSet position 

            //=========
            //Console.WriteLine("\n============================");
            //Console.WriteLine("Adding candidate testSet to candidateSets array: ");
            //for (int i = 0; i < numberParameters; ++i)
            //  Console.Write(testSet[i] + " ");
            //Console.WriteLine("");
            //Console.WriteLine("============================\n");

            candidateSets[candidate] = testSet;  // add candidate testSet to candidateSets array
          } // for each candidate testSet

          //Console.WriteLine("Candidates testSets are: ");
          //for (int i = 0; i < candidateSets.Length; ++i)
          //{
          //  int[] curr = candidateSets[i];
          //  Console.Write(i + ": ");
          //  for (int j = 0; j < curr.Length; ++j)
          //  {
          //    Console.Write(curr[j] + " ");
          //  }
          //  Console.WriteLine(" -- captures " + NumberPairsCaptured(curr, unusedPairsSearch));
          //}

          // Iterate through candidateSets to determine the best candidate

          int indexOfBestCandidate = r.Next(candidateSets.Length); // pick a random index as best
          int mostPairsCaptured = NumberPairsCaptured(candidateSets[indexOfBestCandidate], unusedPairsSearch);

          int[] bestTestSet = new int[numberParameters];

          for (int i = 0; i < candidateSets.Length; ++i)
          {
            int pairsCaptured = NumberPairsCaptured(candidateSets[i], unusedPairsSearch);
            if (pairsCaptured > mostPairsCaptured)
            {
              mostPairsCaptured = pairsCaptured;
              indexOfBestCandidate = i;
            }
            //Console.WriteLine("Candidate " + i + " captured " + mostPairsCaptured);
          }
          //Console.WriteLine("Candidate number " + indexOfBestCandidate + " is best");
          candidateSets[indexOfBestCandidate].CopyTo(bestTestSet, 0);
          testSets.Add(bestTestSet); // Add the best candidate to the main testSets List

          // now perform all updates

          //Console.WriteLine("Updating unusedPairs");
          //Console.WriteLine("Updating unusedCounts");
          //Console.WriteLine("Updating unusedPairsSearch");
          for (int i = 0; i <= numberParameters - 2; ++i)
          {
            for (int j = i + 1; j <= numberParameters - 1; ++j)
            {
              int v1 = bestTestSet[i]; // value 1 of newly added pair
              int v2 = bestTestSet[j]; // value 2 of newly added pair

              //Console.WriteLine("Decrementing the unused counts for " + v1 + " and " + v2);
              --unusedCounts[v1];
              --unusedCounts[v2];

              //Console.WriteLine("Setting unusedPairsSearch at " + v1 + " , " + v2 + " to 0");
              unusedPairsSearch[v1, v2] = 0;

              for (int p = 0; p < unusedPairs.Count; ++p)
              {
                int[] curr = unusedPairs[p];

                if (curr[0] == v1 && curr[1] == v2)
                {
                  //Console.WriteLine("Removing " + v1 + ", " + v2 + " from unusedPairs List");
                  unusedPairs.RemoveAt(p);
                }
              }
            } // j
          } // i

        } // primary while loop

        // Display results

        Console.WriteLine("\nResult testsets: \n");
        for (int i = 0; i < testSets.Count; ++i)
        {
          Console.Write(i.ToString().PadLeft(3) + ": ");
          int[] curr = testSets[i];
          for (int j = 0; j < numberParameters; ++j)
          {
            Console.Write(parameterValues[curr[j]] + " ");
          }
          Console.WriteLine("");
        }

        Console.WriteLine("\nEnd\n");
        Console.ReadLine();
      }
      catch (Exception ex)
      {
        Console.WriteLine("Fatal: " + ex.Message);
        Console.ReadLine();
      }
    } // Main()

    static int NumberPairsCaptured(int[] ts, int[,] unusedPairsSearch)  // number of unused pairs captured by testSet ts 
    {
      int ans = 0;
      for (int i = 0; i <= ts.Length - 2; ++i)
      {
        for (int j = i + 1; j <= ts.Length - 1; ++j)
        {
          if (unusedPairsSearch[ts[i], ts[j]] == 1)
            ++ans;
        }
      }
      return ans;
    } // NumberPairsCaptured()

  } // class
} // ns
