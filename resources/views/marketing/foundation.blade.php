<x-marketing-layout
    title="Marketing foundation"
    description="Phase 3B component library and global marketing shell for Algos CRM."
>
    {{-- Hero shell (content finalized in Phase 3C) --}}
    <section class="mk-atmosphere">
        <div class="mk-container mk-section">
            <div class="mk-fade-up max-w-3xl">
                <p class="mk-eyebrow mb-4">Phase 3B · Foundation</p>
                <h1 class="mk-display text-4xl sm:text-5xl lg:text-6xl">
                    {{ config('marketing.name') }} marketing system
                </h1>
                <p class="mk-lead mt-5 max-w-2xl">
                    Global layout, theme, typography, buttons, and reusable components are ready for review.
                    Home page content ships in Phase 3C.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-marketing.button href="{{ Route::has('register') ? route('register') : route('login') }}" size="lg">
                        Start free trial
                        <x-marketing.icon name="arrow-right" size="sm" />
                    </x-marketing.button>
                    <x-marketing.button href="{{ route('marketing.contact', ['intent' => 'demo']) }}" variant="secondary" size="lg">
                        Book demo
                    </x-marketing.button>
                </div>
            </div>
        </div>
    </section>

    {{-- Buttons --}}
    <section class="mk-section border-t border-slate-200/80 bg-white">
        <div class="mk-container">
            <x-marketing.section-heading
                eyebrow="Buttons"
                title="Consistent actions"
                description="Primary, secondary, soft, and ghost variants with shared sizing."
            />
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <x-marketing.button>Primary</x-marketing.button>
                <x-marketing.button variant="secondary">Secondary</x-marketing.button>
                <x-marketing.button variant="soft">Soft</x-marketing.button>
                <x-marketing.button variant="ghost">Ghost</x-marketing.button>
                <x-marketing.button size="sm">Small</x-marketing.button>
                <x-marketing.button size="lg">Large</x-marketing.button>
            </div>
        </div>
    </section>

    {{-- Feature cards --}}
    <section class="mk-section mk-section-muted">
        <div class="mk-container">
            <x-marketing.section-heading
                eyebrow="Feature cards"
                title="Modular product storytelling"
                description="Reusable cards with Heroicons for every CRM module."
                align="center"
                class="mb-10"
            />
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <x-marketing.feature-card
                    icon="user-plus"
                    title="Lead management"
                    description="Capture, qualify, and convert leads through a clear pipeline."
                />
                <x-marketing.feature-card
                    icon="users"
                    title="Customer management"
                    description="Keep account context, history, and ownership in one place."
                />
                <x-marketing.feature-card
                    icon="kanban"
                    title="Kanban boards"
                    description="Drag-and-drop boards for leads and tasks across your team."
                />
            </div>
        </div>
    </section>

    {{-- Pricing cards --}}
    <section class="mk-section bg-white" x-data="pricingToggle('monthly')">
        <div class="mk-container">
            <x-marketing.section-heading
                eyebrow="Pricing cards"
                title="Plan previews with billing toggle"
                description="Alpine-powered monthly / annual switching for Phase 3E."
                align="center"
            />

            <div class="mt-8 flex justify-center">
                <div class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 p-1" role="group" aria-label="Billing period">
                    <button
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                        :class="!isAnnual() ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
                        @click="setBilling('monthly')"
                    >
                        Monthly
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                        :class="isAnnual() ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'"
                        @click="setBilling('annual')"
                    >
                        Annual
                        <span class="ml-1 text-xs font-medium text-sky-700">{{ config('marketing.pricing.annual_discount_label') }}</span>
                    </button>
                </div>
            </div>

            <div class="mt-10 grid gap-6 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    <div x-show="!isAnnual()">
                        <x-marketing.pricing-card
                            :name="$plan['name']"
                            :description="$plan['description']"
                            :monthly="$plan['monthly']"
                            :annual="$plan['annual']"
                            :features="$plan['features']"
                            :cta="$plan['cta']"
                            :highlighted="$plan['highlighted']"
                            billing="monthly"
                        />
                    </div>
                    <div x-cloak x-show="isAnnual()">
                        <x-marketing.pricing-card
                            :name="$plan['name']"
                            :description="$plan['description']"
                            :monthly="$plan['monthly']"
                            :annual="$plan['annual']"
                            :features="$plan['features']"
                            :cta="$plan['cta']"
                            :highlighted="$plan['highlighted']"
                            billing="annual"
                        />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="mk-section mk-section-muted">
        <div class="mk-container">
            <x-marketing.section-heading
                eyebrow="Testimonials"
                title="Social proof components"
                description="Placeholder quotes ready for the home and pricing pages."
                class="mb-10"
            />
            <div class="grid gap-5 lg:grid-cols-3">
                <x-marketing.testimonial-card
                    quote="We finally have one place for leads, customers, and follow-ups. The team adopted it in a week."
                    name="Maya Chen"
                    role="Head of Sales"
                    company="Northline"
                />
                <x-marketing.testimonial-card
                    quote="Kanban boards and role permissions gave us structure without slowing the reps down."
                    name="Jordan Blake"
                    role="COO"
                    company="Bright Harbor"
                />
                <x-marketing.testimonial-card
                    quote="Importing our CSV data was painless. Reporting helped us see what was actually converting."
                    name="Priya Nair"
                    role="Revenue Ops"
                    company="Cascade Labs"
                />
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="mk-section bg-white">
        <div class="mk-container grid gap-10 lg:grid-cols-[1fr_1.2fr] lg:items-start">
            <x-marketing.section-heading
                eyebrow="FAQ accordion"
                title="Answers without the clutter"
                description="Keyboard-friendly disclosure pattern for marketing FAQs."
            />
            <x-marketing.faq-accordion :items="$faqItems" open="trial" />
        </div>
    </section>

    {{-- CTA --}}
    <x-marketing.cta
        title="Component library complete"
        description="Approve Phase 3B to continue with the full Home page in Phase 3C."
    />
</x-marketing-layout>
