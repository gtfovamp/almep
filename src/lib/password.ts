// Password hashing using Web Crypto API (compatible with Cloudflare Workers)

const ITERATIONS = 100000;
const KEY_LENGTH = 32;
const SALT_LENGTH = 16;

async function pbkdf2(
  password: string,
  salt: Uint8Array,
  iterations: number,
  keyLength: number
): Promise<Uint8Array> {
  const encoder = new TextEncoder();
  const passwordBuffer = encoder.encode(password);

  const importedKey = await crypto.subtle.importKey(
    'raw',
    passwordBuffer,
    { name: 'PBKDF2' },
    false,
    ['deriveBits']
  );

  const derivedBits = await crypto.subtle.deriveBits(
    {
      name: 'PBKDF2',
      salt: salt,
      iterations: iterations,
      hash: 'SHA-256'
    },
    importedKey,
    keyLength * 8
  );

  return new Uint8Array(derivedBits);
}

function arrayBufferToBase64(buffer: Uint8Array): string {
  const bytes = Array.from(buffer);
  const binary = String.fromCharCode(...bytes);
  return btoa(binary);
}

function base64ToArrayBuffer(base64: string): Uint8Array {
  const binary = atob(base64);
  const bytes = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) {
    bytes[i] = binary.charCodeAt(i);
  }
  return bytes;
}

export async function hashPassword(password: string): Promise<string> {
  // Generate random salt
  const salt = crypto.getRandomValues(new Uint8Array(SALT_LENGTH));

  // Hash password
  const hash = await pbkdf2(password, salt, ITERATIONS, KEY_LENGTH);

  // Combine salt and hash, encode as base64
  const combined = new Uint8Array(salt.length + hash.length);
  combined.set(salt, 0);
  combined.set(hash, salt.length);

  return arrayBufferToBase64(combined);
}

export async function verifyPassword(password: string, storedHash: string): Promise<boolean> {
  try {
    // Decode stored hash
    const combined = base64ToArrayBuffer(storedHash);

    // Extract salt and hash
    const salt = combined.slice(0, SALT_LENGTH);
    const originalHash = combined.slice(SALT_LENGTH);

    // Hash the provided password with the same salt
    const hash = await pbkdf2(password, salt, ITERATIONS, KEY_LENGTH);

    // Constant-time comparison to prevent timing attacks
    if (hash.length !== originalHash.length) {
      return false;
    }

    let result = 0;
    for (let i = 0; i < hash.length; i++) {
      result |= hash[i] ^ originalHash[i];
    }

    return result === 0;
  } catch (error) {
    console.error('Password verification error:', error);
    return false;
  }
}
