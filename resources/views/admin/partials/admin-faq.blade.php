@include('admin.partials.pos-faq', [
  'faqTitle' => 'Admin Help & FAQ',
  'faqSubtitle' => 'Find a task or search for a quick answer.',
  'faqShowFab' => false,
  'faqGroups' => [
    'Dashboard & reports' => [
      ['What does the dashboard show?', 'Sales and transaction counts follow the selected dates. Inventory, expiry alerts, menu items and staff counts show the current state. Click a summary card to open the relevant view.'],
      ['How do I change the reporting period?', 'Choose From and To on Dashboard, then Apply. In Reports, select a report and period, then Apply. All calendar dates use Philippine time.'],
      ['How do I export a report?', 'Open Reports, apply your filters, then open the Export menu to choose CSV or PDF.'],
    ],
    'Inventory & purchases' => [
      ['How do I add or restock ingredients?', 'Use Record Purchase in Inventory or Supply Purchases. Add the items, quantities, costs and expiry dates where applicable, then save. The purchase receives stock into inventory.'],
      ['How do expiry alerts work?', 'Batches expiring within 7 days, including today, appear in expiry alerts. Open View batches in Inventory for details. Use the earliest-expiring stock first. Expired batches are excluded from POS availability; record waste after removing them from physical stock.'],
      ['Is an expiry date required for every item?', 'Leave expiry blank for supplies that do not expire. Enter the date printed on the packaging for each purchased batch that does expire.'],
      ['Why is my reference number rejected?', 'A purchase reference must be unique. Check Supply Purchases before saving to avoid recording the same receipt twice. References are trimmed and saved in uppercase; you may leave the reference blank when none is available.'],
      ['How do I find stock or past movements?', 'Use the Inventory search and item tabs, then move between pages. Stock Activity has its own sorting and pagination below the items.'],
    ],
    'Products & sales' => [
      ['Why is a product unavailable in the POS?', 'Check the product recipe and ingredient quantities. Missing or expired stock can make it unavailable. The dashboard lists affected products and links to restock their ingredients.'],
      ['How do I search for a sale?', 'Open Orders, search by product, receipt or staff, set the dates if needed, then press Search. You can also sort the results.'],
      ['How do I update a product picture?', 'Open Products, edit the product, choose an image and save. The uploaded picture is stored for use across the product list and POS.'],
    ],
    'Promos & accounts' => [
      ['How do I disable a promo?', 'Open Promos, edit the promo, uncheck Active and save. It remains in your records but cannot be used for new sales. Check Active again to enable it.'],
      ['How does a cashier apply a promo?', 'Choose an available promo in the POS dropdown. It is checked immediately and the discount appears before charging. Percent discounts must be above 0 and no more than 100; fixed discounts must be positive.'],
      ['Why is a promo missing from the POS?', 'Check whether it is active, archived or past its expiry date. A promo can be used through its expiry date in Philippine time.'],
      ['Where do I manage staff access?', 'Open User Management to manage staff accounts and POS PINs. Staff should use their own PIN so sales are attributed correctly.'],
    ],
  ],
])
