/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React              from 'react';
import CompositeFormField from './CompositeFormField';
import FormControl        from 'react-bootstrap/lib/FormControl';

/**
 * Form component for a selectable field containing `<option>` tags.
 *
 * @param {Object} props The component properties.
 *
 * @returns {React.Element} The markup.
 */
const SelectableField = props => {
  const { name, errors, helper, value, options, ...settings } = props;

  return (
    <CompositeFormField name={name} errors={errors} helper={helper}>
      <FormControl name={name} value={value} onChange={helper.getChangeListener()} componentClass="select" {...settings}>
        {Object.keys(options).map((key, i) => <option value={key} key={i}>{options[key]}</option>)}
      </FormControl>
    </CompositeFormField>
  );
};

SelectableField.propTypes = Object.assign({}, CompositeFormField.propTypes, {
  options: React.PropTypes.object,
  value:   React.PropTypes.string
});

export default SelectableField;
