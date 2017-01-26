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

import React, { Component, PropTypes }                     from 'react';
import counterpart                                         from 'counterpart';
import { FormGroup, FormControl, ControlLabel, HelpBlock } from 'react-bootstrap';
import Translate                                           from 'react-translate-component';
import Locale                                              from '../../util/http/Locale';

/**
 * Form field component which connects `react-bootstrap` with `redux-form`.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
export default class FormField extends Component {
  static propTypes = {
    input: PropTypes.object.isRequired,
    label: PropTypes.string.isRequired,
    type:  PropTypes.string.isRequired,
    meta:  PropTypes.object.isRequired
  };

  constructor(props) {
    super(props);
    this.update = () => this.forceUpdate();
  }

  /**
   * @returns {void}
   */
  componentDidMount() {
    counterpart.onLocaleChange(this.update);
  }

  /**
   * @returns {void}
   */
  componentWillUnmount() {
    counterpart.offLocaleChange(this.update);
  }

  /**
   * Renders the formfield.
   *
   * @returns {React.Element} The component's marup.
   */
  render() {
    const { input, label, type, meta: { touched, error } } = this.props;

    return (
      <FormGroup validationState={touched && (error) ? 'error' : null}>
        <ControlLabel><Translate content={label} /></ControlLabel>
        <FormControl {...input} placeholder={counterpart.translate(label)} type={type} />
        <FormControl.Feedback />
        {
          error
            ? error[Locale.getLocale()].map((msg, i) => <HelpBlock key={i}>{msg}</HelpBlock>)
            : null // @TODO generic language + translation
        }
      </FormGroup>
    );
  }
}
