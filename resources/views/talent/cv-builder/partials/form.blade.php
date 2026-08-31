{{-- Identité --}}
<section class="space-y-3">
    <h3 class="text-sm font-bold text-indigo-900">{{ __('talenma.cv_builder.form.identity') }}</h3>

    <div class="flex flex-wrap items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50/80">
        <div class="shrink-0">
            <template x-if="photoPreview()">
                <img :src="photoPreview()" alt="" class="w-20 h-20 rounded-full object-cover border-2 border-indigo-200">
            </template>
            <template x-if="! photoPreview()">
                <div class="w-20 h-20 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-bold" x-text="(data.full_name || '?').charAt(0)"></div>
            </template>
        </div>
        <div class="flex flex-col gap-2 min-w-[200px]">
            <label class="inline-flex items-center justify-center px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 cursor-pointer hover:border-indigo-300">
                {{ __('talenma.cv_builder.form.upload_photo') }}
                <input type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="onPhotoFile($event)">
            </label>
            <button type="button" x-show="profileAvatarUrl && data.photo_source !== 'profile'" @click="useProfilePhoto()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 text-left">
                {{ __('talenma.cv_builder.form.use_profile_photo') }}
            </button>
            <button type="button" x-show="data.photo_source === 'custom' && data.photo_base64" @click="removeCustomPhoto()" class="text-xs text-gray-500 hover:text-red-600 text-left">
                {{ __('talenma.cv_builder.form.remove_custom_photo') }}
            </button>
            <p class="text-[11px] text-gray-400">{{ __('talenma.cv_builder.form.photo_hint') }}</p>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-3">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('talenma.cv_builder.form.full_name') }}</label>
            <input type="text" x-model="data.full_name" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('talenma.cv_builder.form.headline') }}</label>
            <input type="text" x-model="data.headline" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.headline_hint') }}">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('talenma.cv_builder.form.email') }}</label>
            <input type="email" x-model="data.email" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('talenma.cv_builder.form.phone') }}</label>
            <input type="text" x-model="data.phone" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('talenma.cv_builder.form.city') }}</label>
            <input type="text" x-model="data.city" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">LinkedIn</label>
            <input type="url" x-model="data.linkedin_url" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">GitHub</label>
            <input type="url" x-model="data.github_url" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('talenma.cv_builder.form.portfolio') }}</label>
            <input type="url" x-model="data.portfolio_url" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
    </div>
</section>

{{-- Profil --}}
<section class="space-y-2">
    <h3 class="text-sm font-bold text-indigo-900">{{ __('talenma.cv_builder.form.summary') }}</h3>
    <textarea x-model="data.summary" @input="onDataChange()" rows="4" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
</section>

{{-- Compétences --}}
<section class="space-y-3">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-indigo-900">{{ __('talenma.cv_builder.form.skills') }}</h3>
        <button type="button" @click="addSkillGroup()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ {{ __('talenma.cv_builder.form.add_group') }}</button>
    </div>
    <template x-for="(group, gi) in data.skill_groups" :key="'sg-'+gi">
        <div class="grid sm:grid-cols-5 gap-2 items-start border border-gray-100 rounded-xl p-3">
            <div class="sm:col-span-2">
                <input type="text" x-model="group.label" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.skill_group') }}">
            </div>
            <div class="sm:col-span-3 flex gap-2">
                <input type="text" x-model="group.items" @input="onDataChange()" class="flex-1 rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.skill_items') }}">
                <button type="button" @click="removeSkillGroup(gi)" class="text-gray-400 hover:text-red-600 text-sm px-1" x-show="data.skill_groups.length > 1">×</button>
            </div>
        </div>
    </template>
</section>

{{-- Expériences --}}
<section class="space-y-3">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-indigo-900">{{ __('talenma.cv_builder.form.experience') }}</h3>
        <button type="button" @click="addExperience()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ {{ __('talenma.cv_builder.form.add_experience') }}</button>
    </div>
    <template x-for="(exp, ei) in data.experiences" :key="'exp-'+ei">
        <div class="border border-gray-200 rounded-xl p-4 space-y-2 bg-gray-50/50">
            <div class="flex justify-between items-start gap-2">
                <p class="text-xs font-semibold text-gray-500">{{ __('talenma.cv_builder.form.experience') }} <span x-text="ei + 1"></span></p>
                <button type="button" @click="removeExperience(ei)" class="text-gray-400 hover:text-red-600 text-sm" x-show="data.experiences.length > 1">×</button>
            </div>
            <div class="space-y-2">
                <input type="text" x-model="exp.title" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.job_title') }}">
                <input type="text" x-model="exp.company" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.company') }}">
                <input type="text" x-model="exp.location" @input="onDataChange()" class="w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.location') }}">
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" x-model="exp.start" @input="onDataChange()" class="min-w-0 w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.start') }}">
                    <input type="text" x-model="exp.end" @input="onDataChange()" class="min-w-0 w-full rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.end') }}" :disabled="exp.current">
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-600">
                    <input type="checkbox" x-model="exp.current" @change="onDataChange()" class="rounded border-gray-300 text-indigo-600">
                    {{ __('talenma.cv_builder.form.current') }}
                </label>
            </div>
            <div class="space-y-1">
                <template x-for="(bullet, bi) in exp.bullets" :key="'b-'+ei+'-'+bi">
                    <div class="flex gap-2 min-w-0">
                        <input type="text" x-model="exp.bullets[bi]" @input="onDataChange()" class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.bullet') }}">
                        <button type="button" @click="removeBullet(ei, bi)" class="text-gray-400 hover:text-red-600 px-1" x-show="exp.bullets.length > 1">×</button>
                    </div>
                </template>
                <button type="button" @click="addBullet(ei)" class="text-xs text-indigo-600 font-medium">+ {{ __('talenma.cv_builder.form.add_bullet') }}</button>
            </div>
        </div>
    </template>
</section>

{{-- Formation --}}
<section class="space-y-3">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-indigo-900">{{ __('talenma.cv_builder.form.education') }}</h3>
        <button type="button" @click="addEducation()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ {{ __('talenma.cv_builder.form.add_education') }}</button>
    </div>
    <template x-for="(edu, di) in data.education" :key="'edu-'+di">
        <div class="grid sm:grid-cols-6 gap-2 items-center">
            <input type="text" x-model="edu.degree" @input="onDataChange()" class="sm:col-span-2 rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.degree') }}">
            <input type="text" x-model="edu.school" @input="onDataChange()" class="sm:col-span-3 rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.school') }}">
            <div class="flex gap-1">
                <input type="text" x-model="edu.year" @input="onDataChange()" class="flex-1 rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.year') }}">
                <button type="button" @click="removeEducation(di)" class="text-gray-400 hover:text-red-600" x-show="data.education.length > 1">×</button>
            </div>
        </div>
    </template>
</section>

{{-- Disponibilité --}}
<section class="space-y-2">
    <h3 class="text-sm font-bold text-indigo-900">{{ __('talenma.cv_builder.sections.availability') }}</h3>
    <textarea
        x-model="data.availability_line"
        @input="onDataChange()"
        rows="2"
        class="w-full rounded-lg border-gray-300 text-sm"
        placeholder="{{ __('talenma.cv_builder.form.availability_hint') }}"
    ></textarea>
</section>

{{-- Certifications --}}
<section class="space-y-3">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-indigo-900">{{ __('talenma.cv_builder.form.certifications') }}</h3>
        <button type="button" @click="addCert()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ {{ __('talenma.cv_builder.form.add_cert') }}</button>
    </div>
    <template x-for="(cert, ci) in data.certifications" :key="'cert-'+ci">
        <div class="flex gap-2">
            <input type="text" x-model="data.certifications[ci]" @input="onDataChange()" class="flex-1 rounded-lg border-gray-300 text-sm">
            <button type="button" @click="removeCert(ci)" class="text-gray-400 hover:text-red-600" x-show="data.certifications.length > 1">×</button>
        </div>
    </template>
</section>

{{-- Langues --}}
<section class="space-y-3">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-bold text-indigo-900">{{ __('talenma.cv_builder.form.languages') }}</h3>
        <button type="button" @click="addLanguage()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ {{ __('talenma.cv_builder.form.add_language') }}</button>
    </div>
    <template x-for="(lang, li) in data.languages" :key="'lang-'+li">
        <div class="grid grid-cols-5 gap-2">
            <input type="text" x-model="lang.name" @input="onDataChange()" class="col-span-2 rounded-lg border-gray-300 text-sm" placeholder="{{ __('talenma.cv_builder.form.language') }}">
            <input type="text" x-model="lang.level" @input="onDataChange()" class="col-span-2 rounded-lg border-gray-300 text-sm" placeholder="B2, C1…">
            <button type="button" @click="removeLanguage(li)" class="text-gray-400 hover:text-red-600" x-show="data.languages.length > 1">×</button>
        </div>
    </template>
</section>
