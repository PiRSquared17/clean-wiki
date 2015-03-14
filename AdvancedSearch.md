# Advanced Search #
Clean Wiki content is stored as HTML, therefore conventional text search may not produce the best result in the case where words are broken up with HTML tags.

Clean Wiki search extracts the text content from the HTML and adds line breaks and spaces where needed, then builds a search tree for all the words and stores it in the page header for faster access. Both the extracted content and search tree are stored within the header; one is used for exact matches and the other for word match.

Every time the page content changes, the content is extracted and the tree is rebuilt and stored in the head. This means the search will only search the current content and not it's history. Currently there is no plan to allow page history search but this may change in the future. In either case searching history can easily be achieved but would generally be slower because the search tree will need to be done during the search.

## Searching ##
A search string is broken up into words using a given set of delimiters such as space, commas, brackets, etc. and each word is search within a page using the search which also contains the number of times the word occurs. The page with the most amount of words found and the most amount of occurrence will be ranked the highest and hence show on top.

## Exact Search ##
If a search string is within quotes then the full search string is searched within the extracted content and not the search tree.

## Case Insensitive ##
All searches are case insensitive. This means the both the extracted content and the search tree are stored in lowercase and search strings are converted to lowercase before searching. I'm not sure if this is the best approach but this can be modified if required.

## Search Tree ##
The are two main reasons for a search tree. First is for speed and second is for precision. The search tree has a similar structure to a spell-check tree. Travers the letters of a word until you find it if it exists. This also allows us to find partial words or miss-spelled words.
These words are also returned but with a fractional rank.

## Search Ranking ##
The search result for each page is based on a ranking system and is ordered from highest rank to lowest. The ranking system consists of 2 components: First the number of words found followed by the number of occurrence of these words. Partial words are also ranked with a fraction.