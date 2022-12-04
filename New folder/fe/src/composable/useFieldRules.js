export function useFieldRules() {
  const required = (v) => !!v || "Field is required";
  const minLength = (v) => v.length > 2 || "Please enter atleast 3 characters length";
  const contactNumber = (v) => v.length < 12 || "Contact number should not be greater than 11";
  const minMaxVal = (v) => parseInt(v) < 60 || "Minutes should be less than 60";
  const secMaxVal = (v) => parseInt(v) < 60 || "Seconds should be less than 60";
  const hoursMaxVal = (v) => parseInt(v) < 24 || "Hours should be less than 24";
  const defaultVal = (v) => {
    if(v == '') {
      return "Please set 00 as default value"
    }
  }

  return { required, minLength, contactNumber, minMaxVal, secMaxVal, hoursMaxVal, defaultVal }
}
