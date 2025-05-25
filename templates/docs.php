<!DOCTYPE html>
<html lang="en" class="h-full"> <?php // Added h-full for consistency ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'My Documents'); ?> | Private Vault</title> <?php // Added | Private Vault ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" /> <?php // Added Inter font ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For icons -->
    <style>
        body { 
            font-family: 'Inter', sans-serif; /* Added Inter font to body */
            min-height: 100vh; /* Ensure body takes full height */
            display: flex; /* For flex-col layout */
            flex-direction: column; /* For flex-col layout */
        }
        /* Adjust main content margin for mobile when top navbar is present */
        @media (max-width: 768px) { /* md breakpoint in Tailwind is 768px */
            main.content-area { 
                margin-top: 4rem; /* Approx h-16, Tailwind h-14 is 3.5rem. Ensure this matches navbar.php mobile height. */
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-100 via-gray-100 to-stone-100 antialiased"> <?php // Added consistent bg and antialiased ?>
    <?php require_once __DIR__ . '/navbar.php'; // The Tailwind sidebar ?>

    <main class="content-area ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8"> <?php // Added flex-1 ?>
        <div class="max-w-4xl mx-auto">
            <div class="bg-white shadow-xl rounded-lg p-6 md:p-8"> <?php // Enhanced card styling ?>
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-6"><?php echo htmlspecialchars($pageTitle ?? 'My Documents'); ?></h1> <?php // Slightly larger heading ?>

                <?php if (isset($page_error)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-md relative mb-6 shadow" role="alert"> <?php // Added shadow and consistent spacing ?>
                        <div class="flex">
                            <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5l1.41 1.41L7.83 9l2.58 2.59L9 13l-4-4 4-4z"/></svg></div>
                            <div>
                                <p class="font-bold">Error:</p>
                                <p class="text-sm"><?php echo htmlspecialchars($page_error); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($documents) && !isset($page_error)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600 text-lg mb-4">You have not uploaded any documents yet.</p>
                        <a href="upload.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-upload mr-2"></i> Upload a document now
                        </a>
                    </div>
                <?php elseif (!empty($documents)): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <tr>
                                    <th class="py-3 px-6 text-left">Filename</th>
                                    <th class="py-3 px-6 text-left">Category</th>
                                    <th class="py-3 px-6 text-center">Date Uploaded</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 text-sm font-light divide-y divide-gray-200"> <?php // Changed text-gray-600 to 700 for better contrast, added divide ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition ease-in-out duration-150"> <?php // Enhanced row styling ?>
                                        <td class="py-4 px-6 text-left whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class="fas fa-file-alt mr-3 text-indigo-500 text-lg"></i> <?php // Larger, colored icon ?>
                                                <span class="font-medium"><?php echo htmlspecialchars($doc['file_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-left">
                                            <span class="bg-blue-100 text-blue-700 py-1 px-3 rounded-full text-xs font-medium"><?php echo htmlspecialchars($doc['category']); ?></span> <?php // Styled category ?>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <?php echo htmlspecialchars(date('M d, Y H:i', strtotime($doc['created_at']))); ?>
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank" class="text-indigo-600 hover:text-indigo-800 mr-3 transition ease-in-out duration-150" title="View/Download">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <!-- Add delete/edit links here if needed in future -->
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
