#!/bin/bash
# Test script for PS Theme prompts
# Usage: ./test-prompt.sh <prompt-name> <component-name>

PROMPT_DIR=".github/prompts"
PROMPT_FILE="$1"
COMPONENT="$2"

if [ -z "$PROMPT_FILE" ] || [ -z "$COMPONENT" ]; then
  echo "Usage: ./test-prompt.sh <prompt-name> <component-name>"
  echo ""
  echo "Available prompts:"
  ls -1 $PROMPT_DIR/*.md | xargs -n1 basename | grep -v README
  echo ""
  echo "Example: ./test-prompt.sh audit-component badge"
  exit 1
fi

PROMPT_PATH="$PROMPT_DIR/$PROMPT_FILE.md"

if [ ! -f "$PROMPT_PATH" ]; then
  echo "❌ Prompt not found: $PROMPT_PATH"
  exit 1
fi

echo "=== TESTING PROMPT ==="
echo "Prompt: $PROMPT_FILE"
echo "Component: $COMPONENT"
echo "Date: $(date '+%Y-%m-%d %H:%M:%S')"
echo ""

# Find component location
if [ -d "source/patterns/elements/$COMPONENT" ]; then
  LEVEL="elements"
elif [ -d "source/patterns/components/$COMPONENT" ]; then
  LEVEL="components"
elif [ -d "source/patterns/collections/$COMPONENT" ]; then
  LEVEL="collections"
else
  echo "❌ Component not found: $COMPONENT"
  echo "Searched in: elements/, components/, collections/"
  exit 1
fi

COMPONENT_PATH="source/patterns/$LEVEL/$COMPONENT"

echo "✅ Component found: $COMPONENT_PATH"
echo ""
echo "Files present:"
ls -1 "$COMPONENT_PATH"
echo ""

# Extract prompt template section
echo "=== PROMPT TEMPLATE (first 50 lines) ==="
sed -n '/^## 📋 Prompt Template/,/^##/p' "$PROMPT_PATH" | head -50
echo ""
echo "... (see full prompt in $PROMPT_PATH)"
echo ""
echo "=== NEXT STEPS ==="
echo "1. Copy the prompt template from: $PROMPT_PATH"
echo "2. Replace placeholders:"
echo "   {COMPONENT_NAME} → $COMPONENT"
echo "   {level} → $LEVEL"
echo "3. Paste to AI agent (GitHub Copilot/Claude/ChatGPT)"
echo "4. Review and execute the generated workflow"
