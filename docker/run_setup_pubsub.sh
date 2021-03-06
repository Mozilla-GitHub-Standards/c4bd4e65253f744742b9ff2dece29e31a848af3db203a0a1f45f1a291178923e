#!/bin/bash

# This Source Code Form is subject to the terms of the Mozilla Public
# License, v. 2.0. If a copy of the MPL was not distributed with this
# file, You can obtain one at http://mozilla.org/MPL/2.0/.

set -e

# Set up Pub/Sub topics and subscriptions.

cd /app

./socorro-cmd pubsub delete
./socorro-cmd pubsub create
