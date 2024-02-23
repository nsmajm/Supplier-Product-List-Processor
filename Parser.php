<?php

require_once "Product.php";

class Parser
{
    private $uniqueCombinations = [];
    private $outputFile = null;

    public function setOutputFile(string $outputFile)
    {
        $this->outputFile = $outputFile;
    }

    public function parseFile(string $filePath)
    {
        try {
            $file = fopen($filePath, 'r');
            if (!$file) {
                throw new RuntimeException("Error opening file: $filePath");
            }

            // Read the first line to detect the delimiter
            $firstLine = fgets($file);
            $delimiter = $this->detectDelimiter($firstLine);

            // Set the file pointer back to the beginning
            fseek($file, 0);

            // Read and validate the header
            $header = fgetcsv($file, 0, $delimiter);
            $this->validateHeader($header);

            // Process each row in the file
            foreach ($this->parseFileGenerator($file, $delimiter, $header) as $product) {
                $this->updateUniqueCombinations($product);
            }

            fclose($file);

            // Display the unique combinations and counts
            $this->displayUniqueCombinations();
        } catch (Exception $exception) {
            throw new RuntimeException($exception->getMessage());
        }
    }

    private function detectDelimiter($line): string
    {
        // Detect the delimiter based on the line content
        if (strpos($line, ',') !== false) {
            return ',';
        } elseif (strpos($line, "\t") !== false) {
            return "\t";
        } else {
            throw new RuntimeException("Unable to detect the delimiter in the file.");
        }
    }

    private function validateHeader($header)
    {
        // Define the required fields
        $requiredFields = ['brand_name', 'model_name', 'colour_name', 'gb_spec_name', 'network_name', 'grade_name', 'condition_name'];  // Update field names

        // Check if all required fields are present in the header
        foreach ($requiredFields as $field) {
            if (!in_array($field, $header)) {
                throw new RuntimeException("Required field '$field' not found in the file header.");
            }
        }
    }

    private function parseFileGenerator($file, $delimiter, $header): Generator
    {
        // Read the file in chunks and yield each row
        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            yield $this->createProduct($header, $row);
        }
    }

    private function createProduct($header, $row): Product
    {
        // Create a Product object from the header and row data
        $productData = array_combine($header, $row);
        return new Product(
            $productData['brand_name'] ?? null,
            $productData['model_name'] ?? null,
            $productData['colour_name'] ?? null,
            $productData['gb_spec_name'] ?? null,
            $productData['network_name'] ?? null,
            $productData['grade_name'] ?? null,
            $productData['condition_name'] ?? null
        );
    }

    private function updateUniqueCombinations(Product $product)
    {
        // Update the count of unique combinations (customize as needed)
        $combinationKey = $this->generateCombinationKey($product);
        if (!isset($this->uniqueCombinations[$combinationKey])) {
            $this->uniqueCombinations[$combinationKey] = [
                'product' => $product,
                'count' => 1,
            ];
        } else {
            $this->uniqueCombinations[$combinationKey]['count']++;
        }
    }

    private function generateCombinationKey(Product $product): string
    {
        // Generate a unique key based on product properties for grouping
        return implode('-', array_values((array)$product));
    }

    private function displayUniqueCombinations()
    {
        // Display the unique combinations and counts
        echo "make,model,colour,capacity,network,grade,condition,count\n";

        $file = fopen($this->outputFile, 'a');
        fputcsv($file, ['make', 'model', 'colour', 'capacity', 'network', 'grade', 'condition', 'count'], ',');
        foreach ($this->uniqueCombinations as $combination) {
            $this->displayProduct($file, $combination['product'], $combination['count']);
        }
        fclose($file);
    }

    private function displayProduct($file, Product $product, $count)
    {
        fputcsv($file, [
            $product->make,
            $product->model,
            $product->colour,
            $product->capacity,
            $product->network,
            $product->grade,
            $product->condition,
            $count
        ], ",");
        echo "{$product->make},{$product->model},{$product->colour},{$product->capacity},{$product->network},{$product->grade},{$product->condition},{$count}\n";
    }
}