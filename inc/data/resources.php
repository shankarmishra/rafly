<?php
/**
 * SEED DATA — High-Value Technical Engineering Resources.
 *
 * Drives /resources and /resources/{slug} detailed technical guides.
 * Each entry provides original technical depth (architecture, performance, security, FAQs, CTAs).
 */

return [
    'react-native-app-development' => [
        'slug'       => 'react-native-app-development',
        'title'      => 'React Native Mobile App Architecture & Engineering Guide',
        'category'   => 'Cross-Platform Frameworks',
        'badge'      => 'TECHNICAL GUIDE // REACT NATIVE',
        'intro'      => 'A deep-dive technical overview of React Native architecture, including the New Architecture (Fabric & TurboModules), Hermes JavaScript engine optimization, state management, and native module integration.',
        'author'     => 'RAFly Engineering Team',
        'updated_at' => '2026-09-15',
        'read_time'  => '10 min read',
        'highlights' => [
            'Fabric Renderer & Concurrent UI Threading',
            'Hermes Bytecode Pre-compilation & Instant Startup',
            'Typed TurboModule Native C++ Interop',
            'Zustand & Redux Toolkit State Architectures',
            'Secure Keychain Auth & SSL Pinning Implementation',
        ],
        'sections' => [
            [
                'id'    => 'architecture',
                'title' => '01. React Native New Architecture: Fabric & TurboModules',
                'content' => 'React Native’s legacy architecture relied on an asynchronous JSON bridge to communicate between JavaScript thread execution and native UI components. The New Architecture eliminates the asynchronous bridge bottleneck using JSI (JavaScript Interface), allowing C++ native objects to be directly exposed to JavaScript without serialization overhead.

Fabric, the new rendering engine, provides synchronous UI layout calculations and concurrent thread execution. This ensures that user interactions such as fast scrolling, pan gestures, and complex animations execute cleanly at 60 FPS without frame drops on both iOS and Android viewports.',
            ],
            [
                'id'    => 'performance',
                'title' => '02. Hermes JS Engine & Startup Performance Tuning',
                'content' => 'Startup latency is a critical mobile retention metric. By utilizing Meta’s Hermes JavaScript engine, JS bundle parsing is replaced with bytecode pre-compilation during build time. 

Hermes reduces TTFF (Time to First Frame) by up to 60%, decreases APK/IPA bundle size, and lowers RAM allocation. Paired with React Native Expo or bare CLI optimization, memory leaks are prevented through precise allocation lifecycle management.',
            ],
            [
                'id'    => 'security',
                'title' => '03. Mobile Vault Security & Native Module Hardening',
                'content' => 'Mobile security requires defense-in-depth across storage, network, and memory layers. Tokens and auth keys must never reside in plain AsyncStorage. RAFly implements encrypted Keychain (iOS) and EncryptedSharedPreferences (Android) wrappers.

For network security, public REST/GraphQL backends use SSL Pinning (verifying server certificate fingerprints against public key hashes) to prevent Man-in-the-Middle (MitM) inspection on compromised public Wi-Fi networks.',
            ],
            [
                'id'    => 'backend-integration',
                'title' => '04. REST, GraphQL & Firebase Sync Architecture',
                'content' => 'Mobile apps operate in unpredictable network conditions. RAFly builds offline-first architectures using SQLite or Realm local databases. Inbound REST or GraphQL payloads are validated via Zod schemas, cached locally, and synchronized asynchronously when connectivity restores.',
            ],
        ],
        'faqs' => [
            [
                'q' => 'When should a business choose React Native over Native Swift/Kotlin?',
                'a' => 'React Native is ideal for businesses requiring fast cross-platform deployment on both iOS and Android from a single codebase while maintaining 95%+ code sharing and native UI performance.',
            ],
            [
                'q' => 'Can React Native handle native device features like Bluetooth or Biometrics?',
                'a' => 'Yes. Custom TurboModules allow direct C++/Swift/Kotlin integration for camera hardware, FaceID/TouchID biometrics, Bluetooth LE, and background location tracking.',
            ],
        ],
    ],

    'flutter-app-development' => [
        'slug'       => 'flutter-app-development',
        'title'      => 'Flutter & Dart Mobile Architecture & High-Performance UI Guide',
        'category'   => 'Cross-Platform Frameworks',
        'badge'      => 'TECHNICAL GUIDE // FLUTTER',
        'intro'      => 'An engineering breakdown of Google Flutter and Dart compiler capabilities, Impeller graphics rendering engine, BLoC / Riverpod state patterns, and native platform channel interop.',
        'author'     => 'RAFly Engineering Team',
        'updated_at' => '2026-09-15',
        'read_time'  => '9 min read',
        'highlights' => [
            'Impeller Next-Gen Vulkan/Metal Rendering Engine',
            'Dart AOT (Ahead-Of-Time) Machine Code Compilation',
            'BLoC & Riverpod Reactive State Management',
            'Platform Channels & MethodChannel Native Interop',
            'Background Service Workers & FCM Push Routing',
        ],
        'sections' => [
            [
                'id'    => 'engine',
                'title' => '01. Impeller Graphics Engine & Dart AOT Compilation',
                'content' => 'Unlike web-view or bridge-based frameworks, Flutter compiles Dart code directly into native ARM64 machine code via Ahead-Of-Time (AOT) compilation. Flutter renders UI pixels directly using its own high-performance graphics engine.

With Google’s Impeller engine replacing Skia on iOS and Android, shader compilation jank is completely eliminated. Impeller pre-compiles custom Metal and Vulkan shaders at build time, guaranteeing smooth 60–120 FPS animations.',
            ],
            [
                'id'    => 'state',
                'title' => '02. Reactive State Management with BLoC & Riverpod',
                'content' => 'Scalable Flutter architecture demands strict separation of presentation widgets from business logic. Using the BLoC (Business Logic Component) pattern or Riverpod with Immutable Data States, user events flow sequentially into streams, emitting predictable state updates.

This decoupled structure allows automated unit testing of state transitions without rendering UI widgets, accelerating regression testing.',
            ],
            [
                'id'    => 'native-channels',
                'title' => '03. Platform Channels & Hardware Integration',
                'content' => 'When native OS capabilities (such as Apple APNs, Android Foreground Services, or custom C++ libraries) are required, MethodChannels and FFI (Foreign Function Interface) establish low-latency binary messaging between Dart and native Swift/Kotlin bindings.',
            ],
        ],
        'faqs' => [
            [
                'q' => 'Is Flutter suitable for enterprise applications?',
                'a' => 'Yes. Flutter is used globally by major financial institutions, e-commerce platforms, and enterprise software companies due to its strict type safety, fast rendering, and reliable multi-platform target capability.',
            ],
            [
                'q' => 'How does Flutter perform compared to native Swift or Kotlin?',
                'a' => 'Because Flutter compiles directly to ARM machine code and renders via Metal/Vulkan without a bridge, rendering and CPU performance are virtually indistinguishable from native apps.',
            ],
        ],
    ],

    'android-app-development' => [
        'slug'       => 'android-app-development',
        'title'      => 'Android Native Engineering & Jetpack Compose Architecture Guide',
        'category'   => 'Native Engineering',
        'badge'      => 'TECHNICAL GUIDE // ANDROID',
        'intro'      => 'A technical guide to native Android development using Kotlin, Jetpack Compose declarative UI, Coroutines & Flow async processing, and Google Play Store deployment best practices.',
        'author'     => 'RAFly Engineering Team',
        'updated_at' => '2026-09-15',
        'read_time'  => '8 min read',
        'highlights' => [
            'Kotlin Modern Syntax & Null-Safety Discipline',
            'Jetpack Compose Declarative UI Paradigm',
            'Coroutines & Asynchronous Flow Pipelines',
            'Room Database Local Persistence & Migration',
            'Google Play App Bundle (AAB) & Security Hardening',
        ],
        'sections' => [
            [
                'id'    => 'compose',
                'title' => '01. Jetpack Compose & Declarative UI Layouts',
                'content' => 'Modern Android development uses Jetpack Compose, Google’s unbundled Kotlin-first declarative UI toolkit. Compose eliminates XML layout inflation overhead, enabling state-driven UI recomposition.',
            ],
            [
                'id'    => 'coroutines',
                'title' => '02. Non-Blocking Async with Coroutines & StateFlow',
                'content' => 'Handling network requests, image decoding, and local database queries on the main thread causes application unresponsiveness (ANR errors). Kotlin Coroutines and StateFlow dispatch async I/O operations safely to background worker threads, resuming UI updates seamlessly.',
            ],
        ],
        'faqs' => [
            [
                'q' => 'Why choose native Android (Kotlin) over cross-platform?',
                'a' => 'Native Kotlin development is recommended when an application relies heavily on deep Android OS integrations, hardware sensors, custom background services, or maximum device-specific optimization.',
            ],
        ],
    ],

    'ios-app-development' => [
        'slug'       => 'ios-app-development',
        'title'      => 'Native iOS Architecture & SwiftUI Engineering Guide',
        'category'   => 'Native Engineering',
        'badge'      => 'TECHNICAL GUIDE // iOS',
        'intro'      => 'An engineering overview of native Apple iOS app development using Swift, SwiftUI declarative views, Combine async streams, Keychain security, and App Store review publishing.',
        'author'     => 'RAFly Engineering Team',
        'updated_at' => '2026-09-15',
        'read_time'  => '8 min read',
        'highlights' => [
            'Swift 6 Concurrent Safety & Async/Await',
            'SwiftUI Reactive Component Architecture',
            'Combine Data Stream Binding & Pipelines',
            'Keychain Hardware Cryptography Security',
            'TestFlight Beta & App Store Guidelines Audit',
        ],
        'sections' => [
            [
                'id'    => 'swiftui',
                'title' => '01. SwiftUI & Apple Human Interface Guidelines',
                'content' => 'SwiftUI provides a declarative syntax for constructing iOS, iPadOS, and watchOS interfaces. Integrated directly into iOS rendering pipelines, SwiftUI views automatically adapt across dynamic type, dark mode, and accessibility (VoiceOver) settings.',
            ],
            [
                'id'    => 'security',
                'title' => '02. iOS Keychain & Hardware Security Enclave',
                'content' => 'Sensitive tokens and cryptographic private keys are backed by the Apple Secure Enclave hardware processor. RAFly configures biometric FaceID authentication checks before accessing secure keychain items.',
            ],
        ],
        'faqs' => [
            [
                'q' => 'How does RAFly ensure Apple App Store approval?',
                'a' => 'We pre-audit all iOS applications against Apple App Review Guidelines section 2.1 through 5.6, ensuring Apple Sign-In compliance, complete privacy manifests, and error-free permission requests prior to submission.',
            ],
        ],
    ],

    'mobile-app-architecture' => [
        'slug'       => 'mobile-app-architecture',
        'title'      => 'Decoupled Mobile App Architecture & Offline-First Design',
        'category'   => 'Software Architecture',
        'badge'      => 'TECHNICAL ARCHITECTURE',
        'intro'      => 'A comprehensive blueprint for engineering resilient mobile applications with decoupled presentation layers, offline-first data caching, REST/GraphQL gateways, and mobile security vaults.',
        'author'     => 'RAFly Engineering Team',
        'updated_at' => '2026-09-15',
        'read_time'  => '11 min read',
        'highlights' => [
            'Clean Architecture Layers (UI, Domain, Data)',
            'Offline-First Local SQLite / Realm Synchronization',
            'Sub-100ms Lightweight Payload REST API Specs',
            'Mobile SSL Pinning & Anti-Tampering Hardening',
            'Automated CI/CD TestFlight & Play Beta Pipelines',
        ],
        'sections' => [
            [
                'id'    => 'layers',
                'title' => '01. Clean Architecture: Layer Isolation',
                'content' => 'Enterprise mobile codebases require strict architectural boundaries. Clean Architecture divides code into three distinct layers:
1. Presentation Layer (UI Widgets, View Models)
2. Domain Layer (Use Cases, Business Rules, Entities)
3. Data Layer (Repositories, API Clients, Local Storage)

Changes in network endpoints or UI components do not disrupt core domain rules, ensuring high testability and long-term code stability.',
            ],
            [
                'id'    => 'offline',
                'title' => '02. Offline-First Caching & Conflict Resolution',
                'content' => 'Mobile networks are fundamentally intermittent. An offline-first mobile architecture writes state changes directly to a local encrypted database (SQLite/Realm) immediately, queuing network sync tasks in a background job processor.',
            ],
        ],
        'faqs' => [
            [
                'q' => 'What is offline-first mobile architecture?',
                'a' => 'Offline-first architecture ensures that an application remains fully functional without an active internet connection by reading from and writing to a local encrypted database first, syncing data with cloud backends whenever network connectivity is available.',
            ],
        ],
    ],

    'python-mobile-app-backend' => [
        'slug'       => 'python-mobile-app-backend',
        'title'      => 'Python FastAPI & Django Mobile Backend Architecture',
        'category'   => 'Backend Engineering',
        'badge'      => 'BACKEND ENGINEERING // PYTHON',
        'intro'      => 'An engineering breakdown of designing high-throughput, typed Python REST API backends for iOS and Android applications using FastAPI, PostgreSQL, Redis caching, and Celery async workers.',
        'author'     => 'RAFly Backend Engineering Team',
        'updated_at' => '2026-09-15',
        'read_time'  => '10 min read',
        'highlights' => [
            'FastAPI Async I/O & Automatic Pydantic Validation',
            'PostgreSQL Indexed Schemas & Connection Pooling',
            'Redis In-Memory Session & Response Caching',
            'Celery & Redis Worker Background Tasks',
            'OAuth2, Argon2id & JWT Mobile Token Auth',
        ],
        'sections' => [
            [
                'id'    => 'fastapi',
                'title' => '01. FastAPI Async Endpoints & Strict Data Validation',
                'content' => 'FastAPI leverages Python 3.12+ async/await syntax and Pydantic data schemas to serve mobile API endpoints with sub-20ms latency. Input parameters are strictly validated at runtime, preventing malformed payload exceptions.',
            ],
            [
                'id'    => 'database',
                'title' => '02. Relational PostgreSQL & In-Memory Redis Caching',
                'content' => 'Relational database queries are tuned with B-Tree indexes and SQLAlchemy 2.0 async ORM models. High-traffic endpoints utilize Redis key-value caching to deliver instant mobile data responses without hitting database disks.',
            ],
        ],
        'faqs' => [
            [
                'q' => 'Is Python suitable for high-traffic mobile backends?',
                'a' => 'Yes. Modern Python with FastAPI and async ASGI servers (Uvicorn/Gunicorn) easily handles tens of thousands of concurrent requests while providing type safety, rapid development, and clean integration with AI/ML libraries.',
            ],
        ],
    ],

    'react-native-vs-flutter' => [
        'slug'       => 'react-native-vs-flutter',
        'title'      => 'React Native vs. Flutter: Comprehensive Mobile Engineering Comparison',
        'category'   => 'Framework Comparison',
        'badge'      => 'ENGINEERING COMPARISON',
        'intro'      => 'An unbiased, in-depth architectural comparison between React Native and Flutter for CTOs, product managers, and engineering teams choosing a cross-platform mobile stack.',
        'author'     => 'RAFly Engineering Team',
        'updated_at' => '2026-09-15',
        'read_time'  => '12 min read',
        'highlights' => [
            'Rendering Philosophy: Native UI Components vs Canvas Impeller',
            'Language Ecosystem: JavaScript/TypeScript vs Dart',
            'Startup Latency & Frame Rate Performance',
            'Native Module Integration & Ecosystem Support',
            'Decision Matrix for Business & Product Scope',
        ],
        'sections' => [
            [
                'id'    => 'rendering',
                'title' => '01. Rendering Paradigms: Native Views vs. Canvas Engine',
                'content' => 'The fundamental difference between React Native and Flutter lies in their rendering paradigms:

- React Native maps JavaScript components directly to native iOS (UIKit/SwiftUI) and Android (View/Jetpack Compose) platform controls via JSI/Fabric. This gives apps authentic OS platform fidelity.
- Flutter draws every pixel on an isolated GPU canvas using its Impeller/Skia engine. This guarantees identical UI rendering down to the exact pixel across all device operating systems.',
            ],
            [
                'id'    => 'decision',
                'title' => '02. Decision Framework: Which Framework Should You Choose?',
                'content' => 'Choose React Native if:
- Your team has strong TypeScript/React expertise.
- You desire authentic native platform widgets and native OS look-and-feel.
- You require extensive third-party web ecosystem integration.

Choose Flutter if:
- You require custom visual branding that must look identical across platforms.
- You desire compiled Dart AOT performance with zero platform widget discrepancy.
- You are building high-performance 2D canvas interfaces or heavy custom animations.',
            ],
        ],
        'faqs' => [
            [
                'q' => 'Which framework has better performance: React Native or Flutter?',
                'a' => 'Both frameworks achieve 60 FPS performance when architected correctly. Flutter has slightly lower rendering overhead due to direct Metal/Vulkan GPU drawing, while React Native with Hermes and Fabric delivers fast startup times and authentic native platform feel.',
            ],
        ],
    ],
];
