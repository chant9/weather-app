## **1\. Purpose**

This plan defines the architecture, components, services, UI, database schema, tooling, and acceptance criteria for the Weather Map Application.

Claude Code **must ask clarifying questions before generating any files** to ensure full alignment with this plan.

## **2\. Tech Stack**

* Laravel 11  
* Livewire v3  
* TailwindCSS  
* LeafletJS (CDN)  
* SQLite  
* Pest  
* PHPStan (level 5–6)  
* Laravel Pint  
* Laravel Boost (skills enabled: infer‑conventions, best practices, Livewire, Tailwind, Pest)

## **3\. Project Setup Requirements**

* Fresh Laravel install (`laravel new weather-app`)  
* Livewire installed  
* Tailwind configured  
* Leaflet included via CDN  
* SQLite database (`database/database.sqlite`)  
* No React, Vue, Inertia, Breeze, or SPA scaffolding  
* No authentication  
* No Docker required  
* `boost.json` committed with only the **claude\_code** agent  
* Remove unused agent folders (`.codex`, `.zed`, `.opencode`)

## **4\. Environment Configuration**

`.env.example` should include:

Code  
DB\_CONNECTION=sqlite  
OPENWEATHER\_API\_KEY=your\_public\_key\_here

The OpenWeatherMap key is intentionally public for reviewer convenience.

Reviewers may override it in `.env`.

## **5\. Database Schema**

### **Table: `locations`**

| Column | Type | Notes |
| :---: | ----- | ----- |
| **id** | integer | PK |
| **name** | string | User‑friendly label |
| **lat** | decimal | Latitude |
| **lng** | decimal | Longitude |
| **created\_at** | timestamp |  |

Locations are **global**, not per‑user.

## **6\. UI Architecture**

### **6.1 Full‑screen Map**

* Leaflet map fills entire viewport  
* Light/dark themed tile layers  
* Attribution visible but subtle (Option 1\)  
* “Use my location” button top‑right  
* Default center: Europe (zoom 4–5)  
* Leaflet Map Examples : Interactive maps with leaflet in R Complete ...  
* Tutorials - Leaflet - a JavaScript library for interactive maps  
* Leaflet.markercluster | Marker Clustering plugin for Leaflet  
* Leaflet Map Methods at Diane Godsey blog

### **6.2 Onboarding Overlay**

* Shown only on first visit (localStorage flag)  
* Message: “Click the map to save favourite locations and track weather.”  
* Dismiss button  
* Hidden once user saves a location

### **6.3 Sidebar (Left)**

* Livewire component  
* Collapsible  
* Lists saved locations  
* Clicking a location opens weather panel  
* Delete button  
* Tailwind styling

### **6.4 Weather Panel (Right)**

* Slide‑in panel  
* Shows current weather, hourly forecast, daily forecast, air quality  
* Loading states  
* Error states  
* Close button  
* Responsive (full‑screen on mobile)  
* Weather app | Figma  
* Premium Vector | Weather app ui template editable and vector  
* Weather App interface UI Design on Behance  
* Weather App UI Design | Figma

## **7\. Livewire Components**

### **7.1 MapInteraction**

Responsibilities:

* Initialise Leaflet map  
* Handle click events → emit `locationSelected(lat, lng)`  
* Switch tile layers based on theme  
* Manage attribution styling  
* Listen for “center map on location” events

Files:

* `app/Livewire/MapInteraction.php`  
* `resources/views/livewire/map-interaction.blade.php`

### **7.2 LocationList**

Responsibilities:

* Display saved locations  
* Handle delete actions  
* Emit `locationChosen(id)`  
* Collapsible UI

Files:

* `app/Livewire/LocationList.php`  
* `resources/views/livewire/location-list.blade.php`

### **7.3 AddLocationModal**

Responsibilities:

* Modal for naming/saving a new location  
* Triggered when map is clicked  
* Saves to SQLite  
* Closes on success

Files:

* `app/Livewire/AddLocationModal.php`  
* `resources/views/livewire/add-location-modal.blade.php`

### **7.4 WeatherPanel**

Responsibilities:

* Fetch weather, forecast, air quality  
* Display weather card  
* Slide‑in animation  
* Handle loading \+ error states

Files:

* `app/Livewire/WeatherPanel.php`  
* `resources/views/livewire/weather-panel.blade.php`

## **8\. Services**

### **8.1 WeatherService**

Endpoint: `/data/2.5/weather`  

Returns current weather.

### **8.2 ForecastService**

Endpoint: `/data/2.5/forecast`  

Returns 5‑day / 3‑hour forecast.

### **8.3 AirQualityService**

Endpoint: `/data/2.5/air_pollution`  

Returns AQI \+ pollutants.

### **8.4 Caching**

* Cache responses for 10 minutes  
* Cache key: `weather_{lat}_{lng}`  
* Use Laravel Cache facade

## **9\. Theme System**

* Light/dark toggle stored in localStorage  
* Tailwind `dark:` classes  
* Leaflet tile layer switches based on theme  
* Attribution styled accordingly

## **10\. Map Attribution Strategy**

* Use OpenStreetMap tiles  
* Remove Leaflet prefix  
* Keep attribution visible (legal requirement)  
* Style with Tailwind (opacity, rounded corners)  
* Future enhancement: custom footer or switch to Mapbox/Google Maps

## **11\. Tooling & Quality**

### **11.1 Pint**

* Run `vendor/bin/pint`  
* Enforce PSR‑12

### **11.2 PHPStan**

* Level 5–6  
* Analyse `app/` and `tests/`

### **11.3 Laravel Boost**

* Use for scaffolding Livewire components  
* Use for generating service classes  
* Helps Claude Code produce idiomatic Laravel

## **12\. Testing**

### **12.1 Unit Tests**

* WeatherService  
* ForecastService  
* AirQualityService  
* Use `Http::fake()` for API mocking

### **12.2 Feature Tests**

* Saving locations  
* Deleting locations  
* Rendering map page  
* WeatherPanel loads correct data

### **12.3 Livewire Tests**

* Sidebar interactions  
* Modal behaviour  
* Weather card opening/closing

## **13\. README Structure**

Must include:

* Project overview  
* Installation steps  
* API key explanation  
* Tooling (Pint, PHPStan, Boost)  
* SQLite browsing note  
* Future enhancements

## **14\. Future Enhancements**

* User accounts \+ per‑user locations  
* Google Maps integration  
* Push notifications for weather alerts  
* Background jobs for periodic refresh  
* Redis caching  
* Docker setup  
* Historical weather  
* Radar layers  
* Drag‑and‑drop map markers  
* Multi‑location comparison view

## **15\. Acceptance Criteria**

* Map loads full‑screen  
* User can click map to save a location  
* Sidebar lists saved locations  
* Weather card shows correct data  
* Theme toggle works  
* Attribution visible and styled  
* SQLite persistence works  
* All tests pass  
* Code passes Pint \+ PHPStan  
* No React/Vue/Inertia present  
* No authentication required

## **16\. File Structure (High‑Level)**

```
app/
  Livewire/
    MapInteraction.php
    LocationList.php
    AddLocationModal.php
    WeatherPanel.php
  Services/
    WeatherService.php
    ForecastService.php
    AirQualityService.php

resources/
  views/
    livewire/
      map-interaction.blade.php
      location-list.blade.php
      add-location-modal.blade.php
      weather-panel.blade.php
    layouts/
      app.blade.php

database/
  migrations/
  database.sqlite

routes/
  web.php

PLAN.md
README.md 
```

## **17. Implementation Notes**

A few deliberate deviations from the plan above, agreed during implementation:

* **Laravel 13 + Livewire v4** were used instead of Laravel 11 + Livewire v3 (the versions actually scaffolded by `laravel new`). Nothing in the plan depended on version-specific APIs.
* **PHPStan** started at level 6, then raised to level 8 once the codebase was further along (two `min()`/`max()` non-empty-array errors were the only fixes needed).
* **Typed DTOs** (`app/DataTransferObjects/`) were added for weather, forecast, and air-quality data, and a **`config/weather.php`** file centralises the API base URL, units, cache TTL, and retry settings — both beyond the original plan, added to strengthen the submission.
* **`Http::retry()`** is applied to all three OpenWeatherMap calls for resilience against transient failures.
* **Cache keys** are namespaced per endpoint (`weather_current_*`, `weather_forecast_*`, `weather_air_quality_*`) rather than the single `weather_{lat}_{lng}` pattern in section 8.4, to avoid the three endpoints colliding on the same cache entry.
* **Caching applies to the raw API response**, not the constructed DTO — PHP's readonly objects (holding `Carbon` instances) don't reliably round-trip through native object serialization on file/database cache drivers; caching plain arrays and rebuilding the DTO on each read avoids that entirely.
* `livewire/blaze`, `laravel/pao`, and the root `opencode.json` (all present in the fresh Laravel install, not part of this plan) were left as-is.
* **Onboarding overlay visibility** is driven by whether any locations exist (server-rendered on load, re-shown live if the last saved location is deleted) rather than section 6.2's one-time localStorage flag — a permanent flag meant it never reappeared after a user deleted all their locations, which felt like the wrong behaviour once tested by hand.
* **Map attribution** (section 10) drops Leaflet's own "Leaflet |" prefix (`attributionControl.setPrefix(false)`) and is styled as a subtle, rounded, theme-aware pill; the override needs the `#map` prefix to out-specificity Leaflet's own `.leaflet-container .leaflet-control-attribution` rule.
* **Weather panel resilience**: the three API calls in `WeatherPanel::open()` are now each wrapped independently, so one failing (e.g. forecast) doesn't prevent the other two from loading — only a full failure of all three shows the error state. The exception catch was also narrowed from bare `Throwable` to `ConnectionException|RequestException`, so a genuine PHP bug isn't silently swallowed as "unable to load weather data."
* **Rate limiting**: weather fetches are capped at 30/minute per session (`RateLimiter`), to protect the OpenWeatherMap key from being burned through rapid/abusive clicking.
* **Duplicate locations**: a unique DB index on `(lat, lng)` plus an application-level check in `AddLocationModal::save()` reject saving the same coordinates twice, surfacing a friendly validation message instead of a raw constraint-violation error.