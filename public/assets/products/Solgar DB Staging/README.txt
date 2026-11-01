Solgar catalog staging package

Contents
- products.csv and products.json contain 33 catalog records matched to the six category IDs and Solgar brand ID shown in the project's reference data.
- Category folders contain the referenced PNG product images. Products with more than one distinct photo list both relative paths in the image field.
- Arabic descriptions are two lines. Allergy warnings ask customers to check the label; they are generic and must be verified against each product's actual ingredients.

Before database import
- price is intentionally blank because no prices were supplied. The products table requires a non-NULL price, so fill it before import.
- stock is set to 0 as a safe placeholder; replace it with actual stock counts.
- ingredients are blank because full ingredient labels were not collected.
- image paths are relative to this staging folder. Move the image files to the application's product upload directory and update the paths before importing.
- category_id values 1-6 and brand_id 4 follow the supplied screenshots; confirm those IDs still match the target database before import.
