# This Source Code Form is subject to the terms of the Mozilla Public
# License, v. 2.0. If a copy of the MPL was not distributed with this
# file, You can obtain one at http://mozilla.org/MPL/2.0/.

from django.contrib import admin
from django.contrib import messages
from django.contrib.auth.admin import UserAdmin
from django.contrib.admin.models import LogEntry, ADDITION, CHANGE, DELETION
from django.utils.html import format_html

from crashstats.crashstats.models import (
    BugAssociation,
    GraphicsDevice,
    MissingProcessedCrash,
    MissingProcessedCrashes,
    Platform,
    Product,
    ProductVersion,
    Signature,

    # Middleware
    PriorityJob
)


# Fix the Django Admin User list display so it shows the columns we care about
UserAdmin.list_display = [
    'email',
    'first_name',
    'last_name',
    'is_superuser',
    'is_staff',
    'is_active',
    'date_joined',
    'last_login'
]


ACTION_TO_NAME = {
    ADDITION: 'add',
    CHANGE: 'change',
    DELETION: 'delete'
}


@admin.register(LogEntry)
class LogEntryAdmin(admin.ModelAdmin):
    date_hierarchy = 'action_time'

    list_display = [
        'action_time',
        'user_email',
        'content_type',
        'object_repr',
        'action_name',
        'get_change_message'
    ]

    def user_email(self, obj):
        return obj.user.email

    def action_name(self, obj):
        return ACTION_TO_NAME[obj.action_flag]

    def has_add_permission(self, request):
        return False

    def has_change_permission(self, request, obj=None):
        # FIXME(willkg): If this always returned False, then this modeladmin
        # doesn't show up in the index. However, this means you get a change
        # page that suggests you can change it, but errors out when saving.
        #
        # We can nix this and use has_view_permission when we upgrade to
        # Django 2.1.
        return request.method != 'POST'

    def has_delete_permission(self, request, obj=None):
        return False

    def has_module_permission(self, request):
        return True


@admin.register(BugAssociation)
class BugAssociationAdmin(admin.ModelAdmin):
    list_display = [
        'bug_id',
        'signature'
    ]
    search_fields = [
        'bug_id',
        'signature'
    ]


@admin.register(GraphicsDevice)
class GraphicsDeviceAdmin(admin.ModelAdmin):
    list_display = [
        'id',
        'vendor_hex',
        'adapter_hex',
        'vendor_name',
        'adapter_name'
    ]
    search_fields = [
        'vendor_hex',
        'adapter_hex',
        'vendor_name',
        'adapter_name'
    ]


@admin.register(Platform)
class PlatformAdmin(admin.ModelAdmin):
    list_display = [
        'name',
        'short_name'
    ]


@admin.register(Product)
class ProductAdmin(admin.ModelAdmin):
    list_display = [
        'product_name',
        'sort',
        'is_active'
    ]


@admin.register(ProductVersion)
class ProductVersionAdmin(admin.ModelAdmin):
    list_display = [
        'product_name',
        'release_channel',
        'major_version',
        'release_version',
        'version_string',
        'build_id',
        'archive_url'
    ]

    search_fields = [
        'version_string'
    ]

    list_filter = [
        'major_version',
        'product_name',
        'release_channel',
    ]


@admin.register(Signature)
class SignatureAdmin(admin.ModelAdmin):
    list_display = [
        'signature',
        'first_build',
        'first_date'
    ]
    search_fields = [
        'signature'
    ]


def process_crashes(modeladmin, request, queryset):
    """Process selected missing processed crashes from admin page."""
    priority_api = PriorityJob()
    crash_ids = list(queryset.values_list('crash_id', flat=True))
    priority_api.post(crash_ids=crash_ids)
    messages.add_message(request, messages.INFO, 'Sent %s crashes for processing.' % len(crash_ids))


process_crashes.short_description = 'Process crashes'


@admin.register(MissingProcessedCrashes)
class MissingProcessedCrashesAdmin(admin.ModelAdmin):
    """DEPRECATED."""

    list_display = [
        'crash_id',
        'created',
        'collected_date',
        'is_processed',
        'report_url_linked',
    ]
    actions = [process_crashes]

    def report_url_linked(self, obj):
        return format_html('<a href="{}">{}</a>', obj.report_url(), obj.report_url())


@admin.register(MissingProcessedCrash)
class MissingProcessedCrashAdmin(admin.ModelAdmin):
    list_display = [
        'crash_id',
        'created',
        'collected_date',
        'is_processed',
        'check_processed',
        'report_url_linked',
    ]
    actions = [process_crashes]

    list_filter = ['is_processed']

    def report_url_linked(self, obj):
        return format_html('<a href="{}">{}</a>', obj.report_url(), obj.report_url())
