#!/bin/bash
npx wrangler pages dev dist --compatibility-date=2025-02-04 --d1=DB=almep-db --kv=KV=images --kv=SESSION=almep-session --port=8788
