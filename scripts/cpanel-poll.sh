#!/bin/bash
set -Eeuo pipefail

REPOSITORY_ROOT="/home/kisanworld/repositories/Kassan-world"
STATE_FILE="/home/kisanworld/.kassan-last-deployed"
LOCK_FILE="/home/kisanworld/.kassan-poll.lock"
UAPI_BIN="/bin/uapi"

exec 8>"$LOCK_FILE"
/bin/flock -n 8 || exit 0

cd "$REPOSITORY_ROOT"

if [[ -n "$(git status --porcelain --untracked-files=no)" ]]; then
  echo "Deployment skipped: repository has tracked local changes."
  exit 1
fi

git fetch --prune origin main
git merge --ff-only origin/main

CURRENT_SHA="$(git rev-parse HEAD)"
DEPLOYED_SHA="$(test -f "$STATE_FILE" && /bin/cat "$STATE_FILE" || true)"

if [[ "$CURRENT_SHA" == "$DEPLOYED_SHA" ]]; then
  echo "Already deployed: $CURRENT_SHA"
  exit 0
fi

echo "Requesting deployment for $CURRENT_SHA"
"$UAPI_BIN" VersionControlDeployment create repository_root="$REPOSITORY_ROOT"
