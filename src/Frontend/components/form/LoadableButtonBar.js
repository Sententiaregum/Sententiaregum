/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React,{ Component } from 'react';
import Spinner from 'react-spinkit';
import counterpart from 'counterpart';

/**
 * Component which combines a button bar with a component.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class LoadableButtonBar extends Component {
  /**
   * Constructor.
   *
   * @param {Array} props Component properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);

    this.handler = () => this.forceUpdate();
    this.state   = {
      progress: this.props.progress
    };
  }

  /**
   * Lifecycle hook which establishes a connection between the event manager of the translator
   * and the form wrapper component.
   *
   * @returns {void}
   */
  componentDidMount() {
    counterpart.onLocaleChange(this.handler);
  }

  /**
   * Lifecycle hook which closes the connection between the event manager of the translator and the wrapper.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    counterpart.offLocaleChange(this.handler);
  }

  /**
   * Lifecycle hook to modify the progress flag.
   *
   * @param {Object} nextProps Property changeset.
   *
   * @returns {void}
   */
  componentWillReceiveProps(nextProps) {
    this.setState({ progress: nextProps.progress ? nextProps.progress : false });
  }

  /**
   * Renders the component.
   *
   * @returns {React.Element} The vDOM markup.
   */
  render() {
    let spinner;
    const props = {};
    if (this.state.progress) {
      spinner        = this._renderSpinner();
      props.disabled = 'disabled';
    }

    return (
      <div className="form-group">
        <button type="submit" className="btn btn-primary spinner-btn" {...props}>
          {counterpart.translate(this.props.btnLabel)}
        </button>
        {spinner}
      </div>
    );
  }

  /**
   * Renders the loading spinner.
   *
   * @returns {React.Element} Markup of the loading spinner.
   * @private
   */
  _renderSpinner() {
    return <Spinner spinnerName="double-bounce" noFadeIn={true} className="custom-spinner" />;
  }
}

LoadableButtonBar.propTypes = {
  btnLabel: React.PropTypes.string,
  progress: React.PropTypes.bool
};
