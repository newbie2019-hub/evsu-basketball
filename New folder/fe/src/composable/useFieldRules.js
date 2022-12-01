export function useFieldRules() {
  const required = (v) => !!v || "Field is required";
  const minLength = (v) => v.length > 2 || "Please enter atleast 3 characters length";
  const contactNumber = (v) => v.length < 12 || "Contact number should not be greater than 11";

  return { required, minLength, contactNumber }
}
