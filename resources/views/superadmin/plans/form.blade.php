<div class="sa-form-section">
    <h2 class="sa-form-section__title">General</h2>
    <div class="form-row">
        <div class="form-group col-md-6"><label>Name</label><input class="form-control" name="name" value="{{ old('name', $plan->name) }}" required></div>
        <div class="form-group col-md-6"><label>Slug</label><input class="form-control" name="slug" value="{{ old('slug', $plan->slug) }}" required></div>
    </div>
    <div class="form-group"><label>Short description</label><input class="form-control" name="short_description" value="{{ old('short_description', $plan->short_description) }}"></div>
    <div class="form-group"><label>Full description</label><textarea class="form-control" name="description" rows="4">{{ old('description', $plan->description) }}</textarea></div>
</div>
<div class="sa-form-section">
    <h2 class="sa-form-section__title">Pricing</h2>
    <div class="form-row">
        <div class="form-group col-md-3"><label>Monthly price</label><input class="form-control" type="number" step="0.01" min="0" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price ?? 0) }}" required></div>
        <div class="form-group col-md-3"><label>Yearly price</label><input class="form-control" type="number" step="0.01" min="0" name="yearly_price" value="{{ old('yearly_price', $plan->yearly_price ?? 0) }}" required></div>
        <div class="form-group col-md-2"><label>Currency</label><input class="form-control" name="currency" value="{{ old('currency', $plan->currency ?? 'USD') }}" required></div>
        <div class="form-group col-md-2"><label>Trial days</label><input class="form-control" type="number" min="0" name="trial_days" value="{{ old('trial_days', $plan->trial_days ?? 14) }}" required></div>
        <div class="form-group col-md-2"><label>Sort order</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', $plan->sort_order ?? 0) }}" required></div>
    </div>
</div>
<div class="sa-form-section"><h2 class="sa-form-section__title">Status</h2>
@foreach (['is_active' => 'Active', 'is_public' => 'Public', 'is_featured' => 'Featured', 'is_free' => 'Free'] as $field => $label)
<div class="custom-control custom-checkbox custom-control-inline"><input class="custom-control-input" type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" @checked(old($field, $plan->$field ?? false))><label class="custom-control-label" for="{{ $field }}">{{ $label }}</label></div>
@endforeach
</div>
<div class="sa-form-section"><h2 class="sa-form-section__title">Internal notes</h2><textarea class="form-control" name="notes" rows="3">{{ old('notes', $plan->notes) }}</textarea></div>
