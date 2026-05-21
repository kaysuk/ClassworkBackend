# Assignment 8 - Quick Reference Guide

## 🎯 What is Assignment 8?

Implement a professional MVC (Model-View-Controller) web application framework for the Drupal-Coder portfolio website. The assignment demonstrates:

- URL routing with regex patterns
- Module-based architecture
- Template rendering system
- Clean code organization
- Separation of concerns

## 📂 Directory Structure

```
Ex8/
├── index.php                      # Entry point (main controller)
├── settings.php                   # Configuration & routing
│
├── scripts/
│   └── init.php                   # Dispatcher & helper functions
│
├── modules/
│   └── front.php                  # Portfolio module
│
├── theme/
│   └── page.tpl.php               # Main HTML template
│
├── img/                           # Portfolio images
├── style.css                      # Main stylesheet
├── work_slider.css                # Slider styles
├── footer_style.css               # Footer styles
├── main.js                        # JavaScript
└── slider.js                      # Slider functionality
```

## 🔄 Request Processing Flow

```
1. Browser Request
   ↓
2. index.php (receives & prepares request)
   ↓
3. init.php dispatcher (matches URL to route)
   ↓
4. Module loading (modules/front.php)
   ↓
5. Handler execution (front_get or front_post)
   ↓
6. Content generation (HTML string)
   ↓
7. Template rendering (theme/page.tpl.php)
   ↓
8. HTTP response (HTML + headers)
```

## 📋 Key Files Explained

### index.php - Entry Point
```php
// Loads configuration
include('./settings.php');

// Includes dispatcher
include('init.php');

// Creates request array
$request = array(
  'url' => $_GET['q'],
  'method' => $_SERVER['REQUEST_METHOD'],
  'get' => $_GET,
  'post' => $_POST
);

// Processes request through dispatcher
$response = init($request, $urlconf);

// Sends response to browser
```

**Key Points:**
- Single entry point for all requests
- Prepares request data
- Calls dispatcher
- Handles response output

### settings.php - Configuration
```php
$conf = array(
  'sitename' => 'Drupal-Coder Portfolio',
  'theme' => './theme',
  'charset' => 'UTF-8',
  'clean_urls' => FALSE
);

$urlconf = array(
  '' => array('module' => 'front'),  // root → front module
  '/^about$/' => array('module' => 'about')  // /about → about module
);
```

**Key Points:**
- All site settings in one place
- URL routing configuration
- Easily extensible

### scripts/init.php - Dispatcher
```php
function init($request, $urlconf) {
  // 1. Match URL to route pattern
  // 2. Load module
  // 3. Call handler function (get/post/put/delete)
  // 4. Render template
  // 5. Return response
}

// Helper functions available:
conf($key)              // Get configuration
url($addr, $params)     // Generate URLs
redirect($location)     // Send redirect
theme($name, $data)     // Render template
not_found()             // 404 error
access_denied()         // 403 error
```

**Key Points:**
- Central request processor
- Route matching engine
- Template system

### modules/front.php - Module
```php
function front_get($request) {
  // Handle GET requests
  // Return HTML string
  return render_portfolio();
}

function front_post($request) {
  // Handle POST requests
  // Can process forms
  if ($_POST['action'] == 'contact') {
    // Process contact form
  }
}

function render_portfolio() {
  // Generate HTML content
  return '<div>Content</div>';
}
```

**Key Points:**
- HTTP method-based handlers
- Separate logic from presentation
- Returns content string

### theme/page.tpl.php - Template
```php
<!DOCTYPE html>
<html>
<head>
  <!-- Head content -->
</head>
<body>
  <?php
  // Display module content
  if (!empty($c['#content']['front'])) {
    echo $c['#content']['front'];
  }
  ?>
</body>
</html>
```

**Key Points:**
- Receives content in $c array
- Combines with layout HTML
- Renders final page

## 🚀 How It Works - Example Request

### User visits: `http://localhost/Ex8/index.php`

1. **index.php runs:**
   - Loads settings.php
   - Creates $request array with url = ""
   - Calls init($request, $urlconf)

2. **init.php processes:**
   - Checks $urlconf for url = ""
   - Finds match: `'' => ['module' => 'front']`
   - Includes modules/front.php
   - Calls front_get($request)

3. **front.php executes:**
   - render_portfolio() generates HTML
   - Returns HTML string
   - Result stored in $c['#content']['front']

4. **Template renders:**
   - page.tpl.php receives $c array
   - Outputs HTML with embedded content
   - Adds CSS/JS links

5. **Response sent:**
   - Browser receives complete HTML
   - Renders page with styling
   - Loads JavaScript

## 📝 Adding New Pages

### Step 1: Create Module
Create `modules/about.php`:
```php
<?php
function about_get($request) {
  return '<h1>About Us</h1>...content...';
}

function about_post($request) {
  return about_get($request);  // Same content
}
```

### Step 2: Add Route
Edit `settings.php`:
```php
$urlconf = array(
  '' => array('module' => 'front'),
  '/^about$/' => array('module' => 'about'),  // ← Add this
);
```

### Step 3: Access
Visit: `?q=about` or `Ex8/index.php?q=about`

## 🔒 Routing Patterns

```php
$urlconf = array(
  ''                    // Exact match: root
  '/^about$/'           // Exact match: /about
  '/^blog\/(\d+)$/'     // Pattern with parameter: /blog/123
  '/^user\/[a-z]+/'     // Pattern with character class
  '/^admin\//i'         // Case-insensitive pattern
);
```

## 🔐 Adding Authentication

```php
// In settings.php:
$urlconf = array(
  '/^admin/' => array(
    'module' => 'admin',
    'auth' => 'auth_basic'  // Requires authentication
  )
);

// Create modules/auth_basic.php:
function auth($request, $route) {
  // Check credentials
  if ($authenticated) {
    return NULL;  // Allow access
  } else {
    return array('headers' => array('HTTP/1.1 401 Unauthorized'));
  }
}
```

## 🎨 Working with Templates

### Passing Data to Template
In module:
```php
function mymodule_get($request) {
  return array(
    'data1' => 'value1',
    'data2' => array('key' => 'value')
  );
}
```

In template (theme/page.tpl.php):
```php
<?php echo $c['#content']['mymodule']; ?>
```

## 📊 Advantages of This Architecture

| Benefit | How It Works |
|---------|-------------|
| **Maintainability** | Clear separation of code layers |
| **Extensibility** | Easy to add modules and routes |
| **Reusability** | Common functions, shared templates |
| **Testability** | Each module can be tested independently |
| **Scalability** | Can grow from simple to complex apps |
| **Flexibility** | Supports different HTTP methods |
| **Security** | Centralized auth system |

## 🐛 Debugging Tips

### Check Routing
```php
// In index.php after init():
echo "URL: " . $request['url'];
echo "Method: " . $request['method'];
var_dump($c);  // See what module returned
```

### Check Module Loading
```php
// In init.php
echo "Looking for module: " . $r['module'];
echo "Handler function: " . $func;
```

### Check Template
```php
// In theme/page.tpl.php
var_dump($c);  // See all available variables
```

## 📞 Accessing the Portfolio

### From Main Index:
1. Open: `http://localhost/Web/Classwork2/index.html`
2. Click: "Задание 8 (Портфолио с фреймворком)"

### Direct Link:
- URL: `http://localhost/Web/Classwork2/Ex8/index.php`

### With Query Parameter:
- Root: `?q=` (empty) or no query
- Portfolio: `?q=portfolio`
- About: `?q=about`

## ✨ Features Preserved

- ✅ Interactive sliders
- ✅ Vue.js modals
- ✅ Contact forms
- ✅ Responsive design
- ✅ All original styling
- ✅ All JavaScript functionality

## 🎓 Learning Outcomes

This assignment teaches:
1. **MVC Architecture** - Separation of concerns
2. **URL Routing** - Pattern-based request mapping
3. **Module System** - Pluggable architecture
4. **Template Engines** - Content rendering
5. **HTTP Handling** - Request/response cycle
6. **Code Organization** - Professional structure
7. **Design Patterns** - Factory, Strategy, Front Controller
8. **PHP Techniques** - Output buffering, dynamic includes

---

**Status:** ✅ Assignment 8 Complete and Ready

For detailed documentation, see:
- IMPLEMENTATION_SUMMARY.md
- FRAMEWORK_README.md
- ASSIGNMENT8_COMPLETION.md
